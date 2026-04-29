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

class DashboardController extends Controller
{
    /**
     * Mendapatkan unit kerja yang bisa diakses user untuk approval
     * Hanya untuk role yang melakukan approval (bukan Auditi)
     */
    private function getAccessibleUnitIds($user): ?array
    {
        $unitIds = collect();
        
        // Unit sendiri
        if ($user->unit_kerja_id) {
            $unitIds->push($user->unit_kerja_id);
        }
        
        // Unit bawahan (subordinates)
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

        // Cek role
        $isAuditi = $user->hasRole('Auditi');
        $isAtasanAuditi = $user->can('approve_stage_1');
        $isTimMonitoring = $user->can('approve_stage_2');
        $isPengendaliTeknis = $user->can('approve_stage_3');
        $isPengendaliMutu = $user->can('approve_stage_4');
        $isPenanggungJawab = $user->can('approve_stage_5');
        
        // Role yang melakukan approval (bukan Auditi)
        $isApprover = $isAtasanAuditi || $isTimMonitoring || $isPengendaliTeknis || $isPengendaliMutu || $isPenanggungJawab;

        $keputusanQuery = Keputusan::query();
        $tindakLanjutQuery = TindakLanjut::query();
        $arahanQuery = Arahan::query();

        // ============================================================
        // FILTER UNTUK AUDITI (hanya lihat unit sendiri)
        // ============================================================
        if ($isAuditi) {
            $unitId = $user->unit_kerja_id;
            
            // Tindak Lanjut: filter berdasarkan unit_kerja_id
            $tindakLanjutQuery->where('unit_kerja_id', $unitId);
            
            // Arahan: filter berdasarkan PIC (arahan_pic)
            $arahanQuery->whereHas('pics', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
            
            // Keputusan: filter berdasarkan arahan yang menjadi PIC
            $keputusanQuery->whereHas('arahan.pics', function($q) use ($user) {
                $q->where('users.id', $user->id);
            });
        }
        
        // ============================================================
        // FILTER UNTUK APPROVER (Atasan Auditi, Tim Monitoring, dll)
        // ============================================================
        elseif ($isApprover) {
            // Cek stage berapa user ini
            $currentStage = null;
            for ($stage = 1; $stage <= 5; $stage++) {
                if ($user->can("approve_stage_{$stage}")) {
                    $currentStage = $stage;
                    break;
                }
            }
            
            // Jika Stage 1 (Atasan Auditi): batasi unit sendiri + bawahan
            if ($currentStage === 1) {
                $accessibleUnitIds = $this->getAccessibleUnitIds($user);
                
                if ($accessibleUnitIds !== null) {
                    // Tindak Lanjut: hanya unit yang bisa diakses
                    $tindakLanjutQuery->whereIn('unit_kerja_id', $accessibleUnitIds);
                    
                    // Arahan: filter berdasarkan PIC yang unitnya bisa diakses
                    $arahanQuery->whereHas('pics.unitKerja', function($q) use ($accessibleUnitIds) {
                        $q->whereIn('unit_kerja.id', $accessibleUnitIds);
                    });
                    
                    // Keputusan: filter sama seperti arahan
                    $keputusanQuery->whereHas('arahan.pics.unitKerja', function($q) use ($accessibleUnitIds) {
                        $q->whereIn('unit_kerja.id', $accessibleUnitIds);
                    });
                }
            }
            // Stage 2-5: bisa lihat semua unit (tidak perlu filter tambahan)
        }
        
        // ============================================================
        // ADMIN: bisa lihat semua (tidak perlu filter)
        // ============================================================

        // Statistik Keputusan
        $keputusanStats = $keputusanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $keputusanStats = array_merge([
            'BD' => 0,
            'BS' => 0,
            'S' => 0,
            'TD' => 0,
        ], $keputusanStats);

        // Statistik Arahan
        $arahanStats = $arahanQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $arahanStats = array_merge([
            'draft' => 0,
            'terkirim' => 0,
            'Terkirim' => 0,
            'BS' => 0,
            'S' => 0,
            'td' => 0,
        ], $arahanStats);

        $totalArahanTerkirim = ($arahanStats['terkirim'] ?? 0) + ($arahanStats['Terkirim'] ?? 0);

        // Statistik Unit Kerja untuk Chart Bar
        if (!$isAuditi && !$isApprover) {
            // Admin: melihat semua distribusi unit
            $unitKerjaStats = UnitKerja::withCount('tindakLanjut')->get()
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'total' => $u->tindak_lanjut_count]);
            $unitKerjaList = UnitKerja::orderBy('name')->get();
        } 
        elseif ($isAuditi) {
            // Auditi: hanya melihat bar unitnya sendiri
            $unitKerjaStats = UnitKerja::where('id', $user->unit_kerja_id)
                ->withCount('tindakLanjut')->get()
                ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'total' => $u->tindak_lanjut_count]);
            $unitKerjaList = collect([]);
        }
        elseif ($isApprover) {
            // Approver: lihat unit yang bisa diakses
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
                        ->withCount('tindakLanjut')->get()
                        ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'total' => $u->tindak_lanjut_count]);
                    $unitKerjaList = UnitKerja::whereIn('id', $accessibleUnitIds)->orderBy('name')->get();
                } else {
                    $unitKerjaStats = collect([]);
                    $unitKerjaList = collect([]);
                }
            } else {
                // Stage 2-5: lihat semua unit
                $unitKerjaStats = UnitKerja::withCount('tindakLanjut')->get()
                    ->map(fn($u) => ['id' => $u->id, 'name' => $u->name, 'total' => $u->tindak_lanjut_count]);
                $unitKerjaList = UnitKerja::orderBy('name')->get();
            }
        }
        else {
            $unitKerjaStats = collect([]);
            $unitKerjaList = collect([]);
        }

        // Statistik Tindak Lanjut
        $tindakLanjutStats = $tindakLanjutQuery->select('status', DB::raw('count(*) as total'))
            ->groupBy('status')->pluck('total', 'status')->toArray();

        $tindakLanjutStats = array_merge([
            'draft' => 0,
            'in_approval' => 0,
            'approved' => 0,
            'rejected' => 0,
            'td' => 0,
        ], $tindakLanjutStats);

        // ============================================================
        // PENDING APPROVALS (yang perlu diapprove oleh user ini)
        // ============================================================
        $pendingApprovalsQuery = Approval::with(['tindakLanjut.unitKerja'])->where('status', 'pending');

        // Cek permission stage user ini
        if ($isAtasanAuditi) {
            $pendingApprovalsQuery->where('stage', 1);
        } elseif ($isTimMonitoring) {
            $pendingApprovalsQuery->where('stage', 2);
        } elseif ($isPengendaliTeknis) {
            $pendingApprovalsQuery->where('stage', 3);
        } elseif ($isPengendaliMutu) {
            $pendingApprovalsQuery->where('stage', 4);
        } elseif ($isPenanggungJawab) {
            $pendingApprovalsQuery->where('stage', 5);
        } else {
            // Auditi atau user lain tidak punya pending approvals
            $pendingApprovalsQuery->whereRaw('1 = 0');
        }

        // Filter berdasarkan unit yang bisa diakses (khusus Atasan Auditi stage 1)
        if ($isAtasanAuditi) {
            $accessibleUnitIds = $this->getAccessibleUnitIds($user);
            if ($accessibleUnitIds !== null) {
                $pendingApprovalsQuery->whereHas('tindakLanjut', function($q) use ($accessibleUnitIds) {
                    $q->whereIn('unit_kerja_id', $accessibleUnitIds);
                });
            } else {
                $pendingApprovalsQuery->whereRaw('1 = 0');
            }
        }

        $pendingApprovals = $pendingApprovalsQuery->latest()->take(10)->get();

        return view('dashboard', [
            'keputusanStats'      => $keputusanStats,
            'tindakLanjutStats'   => $tindakLanjutStats,
            'arahanStats'         => $arahanStats,
            'unitKerjaStats'      => $unitKerjaStats,
            'unitKerjaList'       => $unitKerjaList,
            'is_global'           => !$isAuditi && !$isApprover,
            'totalKeputusan'      => array_sum($keputusanStats),
            'totalArahan'         => array_sum($arahanStats),
            'totalArahanTerkirim' => $totalArahanTerkirim,
            'pendingApprovals'    => $pendingApprovals,
        ]);
    }
}