<?php

namespace App\Http\Controllers;

use App\Models\Keputusan;
use App\Models\TindakLanjut;
use App\Models\UnitKerja;
use App\Models\Arahan;
use App\Models\Approval;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class DashboardController extends Controller
{
    private function getAccessibleUnitIds($user): ?array
    {
        $unitIds = collect();

        if ($user->unit_kerja_id) {
            $unitIds->push($user->unit_kerja_id);
        }

        $subordinateUnits = User::where('pic_unit_kerja_id', $user->id)
            ->whereNotNull('unit_kerja_id')
            ->pluck('unit_kerja_id');

        $unitIds = $unitIds->concat($subordinateUnits)->unique()->values()->toArray();

        return !empty($unitIds) ? $unitIds : null;
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $isAuditi           = $user->hasRole('Auditi');
        $isAtasanAuditi     = $user->can('approve_stage_1');
        $isTimMonitoring    = $user->can('approve_stage_2');
        $isPengendaliTeknis = $user->can('approve_stage_3');
        $isPengendaliMutu   = $user->can('approve_stage_4');
        $isPenanggungJawab  = $user->can('approve_stage_5');

        $isApprover = $isAtasanAuditi || $isTimMonitoring || $isPengendaliTeknis || $isPengendaliMutu || $isPenanggungJawab;

        // Query dasar untuk setiap model
        $keputusanQuery    = Keputusan::query();
        $tindakLanjutQuery = TindakLanjut::query();
        $arahanQuery       = Arahan::query();

        // Terapkan filter berdasarkan role
        if ($isAuditi) {
            $unitId = $user->unit_kerja_id;
            $tindakLanjutQuery->where('unit_kerja_id', $unitId);
            $arahanQuery->whereHas('pics', fn($q) => $q->where('users.id', $user->id));
            $keputusanQuery->whereHas('arahan.pics', fn($q) => $q->where('users.id', $user->id));
        } elseif ($isApprover) {
            $currentStage = null;
            for ($stage = 1; $stage <= 5; $stage++) {
                if ($user->can("approve_stage_{$stage}")) {
                    $currentStage = $stage;
                    break;
                }
            }

            if ($currentStage === 1) {
                $accessibleUnitIds = $this->getAccessibleUnitIds($user);
                if ($accessibleUnitIds !== null) {
                    $tindakLanjutQuery->whereIn('unit_kerja_id', $accessibleUnitIds);
                    $arahanQuery->whereHas('pics.unitKerja', fn($q) => $q->whereIn('unit_kerja.id', $accessibleUnitIds));
                    $keputusanQuery->whereHas('arahan.pics.unitKerja', fn($q) => $q->whereIn('unit_kerja.id', $accessibleUnitIds));
                }
            }
        }

        // PERBAIKAN: Buat query terpisah untuk recent activities (tanpa group by)
        $recentActivitiesQuery = TindakLanjut::query();
        if ($isAuditi) {
            $recentActivitiesQuery->where('unit_kerja_id', $user->unit_kerja_id);
        } elseif ($isApprover) {
            $currentStage = null;
            for ($stage = 1; $stage <= 5; $stage++) {
                if ($user->can("approve_stage_{$stage}")) {
                    $currentStage = $stage;
                    break;
                }
            }
            if ($currentStage === 1) {
                $accessibleUnitIds = $this->getAccessibleUnitIds($user);
                if ($accessibleUnitIds !== null) {
                    $recentActivitiesQuery->whereIn('unit_kerja_id', $accessibleUnitIds);
                }
            }
        }

        // Statistik Keputusan Detail
        $keputusanStats = $keputusanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
        $keputusanStats = array_merge(['BD' => 0, 'BS' => 0, 'S' => 0, 'TD' => 0], $keputusanStats);
        
        $statusKeputusanLabels = [
            'BD' => 'Belum Ditindaklanjuti',
            'BS' => 'Belum Selesai', 
            'S' => 'Selesai',
            'TD' => 'Tidak Ditindaklanjuti'
        ];

        // Periode terbaru & statistik per periode
        $periodeTermbaru = Keputusan::max('periode_year') ?? '-';
        $periodeStats = Keputusan::select('periode_year', DB::raw('count(*) as total'))
            ->groupBy('periode_year')
            ->orderBy('periode_year', 'desc')
            ->take(3)
            ->get();

        // Statistik Arahan Detail
        $arahanStats = $arahanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
        $arahanStats = array_merge([
            'draft' => 0, 'terkirim' => 0, 'Terkirim' => 0, 'BS' => 0, 'S' => 0, 'td' => 0,
        ], $arahanStats);

        $totalArahan = array_sum($arahanStats);
        $totalArahanTerkirim = ($arahanStats['terkirim'] ?? 0) + ($arahanStats['Terkirim'] ?? 0);
        $totalArahanSelesai = $arahanStats['S'] ?? 0;

        // Arahan belum ada tindak lanjut
        $arahanBelumTL = (clone $arahanQuery)->whereDoesntHave('tindakLanjut')->count();

        // Arahan dengan progress - hitung secara terpisah
        $arahanWithTL = (clone $arahanQuery)->whereHas('tindakLanjut')->get();
        $arahanProgress = $arahanWithTL->map(function($arahan) {
            $tindakLanjut = $arahan->tindakLanjut();
            return [
                'arahan' => $arahan->arahan,
                'total_tl' => $tindakLanjut->count(),
                'approved_tl' => (clone $tindakLanjut)->where('status', 'approved')->count(),
                'pending_tl' => (clone $tindakLanjut)->where('status', 'pending')->count(),
            ];
        });

        // Statistik Unit Kerja
        if (!$isAuditi && !$isApprover) {
            $unitKerjaStats = UnitKerja::withCount([
                'tindakLanjut',
                'tindakLanjut as approved_count' => fn($q) => $q->where('status', 'approved'),
                'tindakLanjut as pending_count' => fn($q) => $q->where('status', 'pending'),
                'tindakLanjut as rejected_count' => fn($q) => $q->where('status', 'rejected')
            ])->get()->map(fn($u) => [
                'id' => $u->id,
                'name' => $u->name,
                'total' => $u->tindak_lanjut_count,
                'approved' => $u->approved_count,
                'pending' => $u->pending_count,
                'rejected' => $u->rejected_count,
                'completion_rate' => $u->tindak_lanjut_count > 0 
                    ? round(($u->approved_count / $u->tindak_lanjut_count) * 100, 1) 
                    : 0
            ]);
            $unitKerjaList = UnitKerja::orderBy('name')->get();
        } elseif ($isAuditi) {
            $unitKerjaStats = UnitKerja::where('id', $user->unit_kerja_id)
                ->withCount([
                    'tindakLanjut',
                    'tindakLanjut as approved_count' => fn($q) => $q->where('status', 'approved'),
                    'tindakLanjut as pending_count' => fn($q) => $q->where('status', 'pending'),
                    'tindakLanjut as rejected_count' => fn($q) => $q->where('status', 'rejected')
                ])->get()->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'total' => $u->tindak_lanjut_count,
                    'approved' => $u->approved_count,
                    'pending' => $u->pending_count,
                    'rejected' => $u->rejected_count,
                    'completion_rate' => $u->tindak_lanjut_count > 0 
                        ? round(($u->approved_count / $u->tindak_lanjut_count) * 100, 1) 
                        : 0
                ]);
            $unitKerjaList = collect([]);
        } elseif ($isApprover) {
            $currentStage = null;
            for ($stage = 1; $stage <= 5; $stage++) {
                if ($user->can("approve_stage_{$stage}")) {
                    $currentStage = $stage;
                    break;
                }
            }

            if ($currentStage === 1) {
                $accessibleUnitIds = $this->getAccessibleUnitIds($user);
                if ($accessibleUnitIds !== null) {
                    $unitKerjaStats = UnitKerja::whereIn('id', $accessibleUnitIds)
                        ->withCount([
                            'tindakLanjut',
                            'tindakLanjut as approved_count' => fn($q) => $q->where('status', 'approved'),
                            'tindakLanjut as pending_count' => fn($q) => $q->where('status', 'pending'),
                            'tindakLanjut as rejected_count' => fn($q) => $q->where('status', 'rejected')
                        ])->get()->map(fn($u) => [
                            'id' => $u->id,
                            'name' => $u->name,
                            'total' => $u->tindak_lanjut_count,
                            'approved' => $u->approved_count,
                            'pending' => $u->pending_count,
                            'rejected' => $u->rejected_count,
                            'completion_rate' => $u->tindak_lanjut_count > 0 
                                ? round(($u->approved_count / $u->tindak_lanjut_count) * 100, 1) 
                                : 0
                        ]);
                    $unitKerjaList = UnitKerja::whereIn('id', $accessibleUnitIds)->orderBy('name')->get();
                } else {
                    $unitKerjaStats = collect([]);
                    $unitKerjaList  = collect([]);
                }
            } else {
                $unitKerjaStats = UnitKerja::withCount([
                    'tindakLanjut',
                    'tindakLanjut as approved_count' => fn($q) => $q->where('status', 'approved'),
                    'tindakLanjut as pending_count' => fn($q) => $q->where('status', 'pending'),
                    'tindakLanjut as rejected_count' => fn($q) => $q->where('status', 'rejected')
                ])->get()->map(fn($u) => [
                    'id' => $u->id,
                    'name' => $u->name,
                    'total' => $u->tindak_lanjut_count,
                    'approved' => $u->approved_count,
                    'pending' => $u->pending_count,
                    'rejected' => $u->rejected_count,
                    'completion_rate' => $u->tindak_lanjut_count > 0 
                        ? round(($u->approved_count / $u->tindak_lanjut_count) * 100, 1) 
                        : 0
                ]);
                $unitKerjaList = UnitKerja::orderBy('name')->get();
            }
        } else {
            $unitKerjaStats = collect([]);
            $unitKerjaList  = collect([]);
        }

        // Statistik Tindak Lanjut Detail
        $tindakLanjutStats = (clone $tindakLanjutQuery)->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();
        $tindakLanjutStats = array_merge([
            'pending' => 0, 'in_approval' => 0, 'approved' => 0, 'rejected' => 0, 'td' => 0,
        ], $tindakLanjutStats);
        
        $totalTindakLanjut = array_sum($tindakLanjutStats);
        $completionRate = $totalTindakLanjut > 0 
            ? round(($tindakLanjutStats['approved'] / $totalTindakLanjut) * 100, 1) 
            : 0;

        // Tindak lanjut per bulan untuk chart
        $monthlyTL = (clone $tindakLanjutQuery)->select(
            DB::raw('MONTH(created_at) as month'),
            DB::raw('YEAR(created_at) as year'),
            DB::raw('count(*) as total'),
            DB::raw('SUM(CASE WHEN status = "approved" THEN 1 ELSE 0 END) as approved'),
            DB::raw('SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected')
        )
        ->whereYear('created_at', date('Y'))
        ->groupBy('year', 'month')
        ->orderBy('year')
        ->orderBy('month')
        ->get();

        // Pending Approvals
        $pendingApprovalsQuery = Approval::with(['tindakLanjut.unitKerja', 'tindakLanjut.arahan'])
            ->where('status', 'pending');

        if ($isAtasanAuditi)         $pendingApprovalsQuery->where('stage', 1);
        elseif ($isTimMonitoring)    $pendingApprovalsQuery->where('stage', 2);
        elseif ($isPengendaliTeknis) $pendingApprovalsQuery->where('stage', 3);
        elseif ($isPengendaliMutu)   $pendingApprovalsQuery->where('stage', 4);
        elseif ($isPenanggungJawab)  $pendingApprovalsQuery->where('stage', 5);
        else                         $pendingApprovalsQuery->whereRaw('1 = 0');

        if ($isAtasanAuditi) {
            $accessibleUnitIds = $this->getAccessibleUnitIds($user);
            if ($accessibleUnitIds !== null) {
                $pendingApprovalsQuery->whereHas('tindakLanjut', fn($q) => $q->whereIn('unit_kerja_id', $accessibleUnitIds));
            } else {
                $pendingApprovalsQuery->whereRaw('1 = 0');
            }
        }

        $pendingApprovals = $pendingApprovalsQuery->latest()->take(10)->get();
        
        // PERBAIKAN: Recent activities menggunakan query terpisah
        $recentActivities = $recentActivitiesQuery
            ->with(['unitKerja', 'arahan'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard', [
            'keputusanStats'        => $keputusanStats,
            'statusKeputusanLabels' => $statusKeputusanLabels,
            'tindakLanjutStats'     => $tindakLanjutStats,
            'arahanStats'           => $arahanStats,
            'unitKerjaStats'        => $unitKerjaStats,
            'unitKerjaList'         => $unitKerjaList,
            'monthlyTL'             => $monthlyTL,
            'is_global'             => !$isAuditi && !$isApprover,
            'totalKeputusan'        => array_sum($keputusanStats),
            'totalArahan'           => $totalArahan,
            'totalArahanTerkirim'   => $totalArahanTerkirim,
            'totalArahanSelesai'    => $totalArahanSelesai,
            'totalTindakLanjut'     => $totalTindakLanjut,
            'completionRate'        => $completionRate,
            'periodeTermbaru'       => $periodeTermbaru,
            'periodeStats'          => $periodeStats,
            'arahanBelumTL'         => $arahanBelumTL,
            'pendingApprovals'      => $pendingApprovals,
            'recentActivities'      => $recentActivities,
            'arahanProgress'        => $arahanProgress,
        ]);
    }
}