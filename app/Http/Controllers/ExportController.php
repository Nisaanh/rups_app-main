<?php

namespace App\Http\Controllers;

use App\Exports\TindakLanjutExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Arahan;
use App\Models\Keputusan;
use App\Models\TindakLanjut;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExportController extends Controller
{
    public function index(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $isAdmin = $user->hasRole([
            'Admin',
            'Tim Monitoring',
            'Penanggung Jawab',
            'Pengendali Mutu',
            'Pengendali Teknis'
        ]);

        $unitKerjaList = collect();
        if ($isAdmin) {
            $unitKerjaList = UnitKerja::orderBy('name')->get();
        } elseif ($user->hasRole('Atasan Auditi')) {
            $unitKerjaList = UnitKerja::whereIn(
                'id',
                $user->subordinates->pluck('unit_kerja_id')->unique()
            )->get();
        }

        $keputusanList = Keputusan::orderByDesc('periode_year')->get();
        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $keputusanList = Keputusan::whereHas(
                'arahan',
                fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id)
            )->orderByDesc('periode_year')->get();
        }

        $query = TindakLanjut::with([
            'arahan.keputusan',
            'arahan.unitKerja',
            'unitKerja',
            'creator',
            'approvals',
        ]);

        if ($isAdmin) {
            if ($request->filled('unit_kerja_id')) {
                $query->where('unit_kerja_id', $request->unit_kerja_id);
            }
        } elseif ($user->hasRole('Atasan Auditi')) {
            $subordinateUnitIds = $user->subordinates->pluck('unit_kerja_id')->unique();
            $query->whereIn('unit_kerja_id', $subordinateUnitIds);
            if ($request->filled('unit_kerja_id')) {
                $query->where('unit_kerja_id', $request->unit_kerja_id);
            }
        } else {
            $query->where('unit_kerja_id', $user->unit_kerja_id)
                ->where('created_by', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('keputusan_id')) {
            $query->whereHas('arahan', fn($q) => $q->where('keputusan_id', $request->keputusan_id));
        }
        if ($request->filled('periode_bulan')) {
            $query->where('periode_bulan', $request->periode_bulan);
        }
        if ($request->filled('periode_tahun')) {
            $query->where('periode_tahun', $request->periode_tahun);
        }

        $data  = $query->latest()->paginate(15)->withQueryString();
        $total = $query->count();

        return view('export.index', compact(
            'isAdmin',
            'unitKerjaList',
            'keputusanList',
            'data',
            'total'
        ));
    }

    public function download(Request $request)
    {
        /** @var \App\Models\User $user */
        $user    = Auth::user();
        $isAdmin = $user->hasRole([
            'Admin',
            'Tim Monitoring',
            'Penanggung Jawab',
            'Pengendali Mutu',
            'Pengendali Teknis'
        ]);

        $jenis = $request->input('jenis', 'tindaklanjut');

        if ($jenis === 'keputusan') {
            return $this->exportKeputusan($request, $user, $isAdmin);
        }

        return $this->exportTindakLanjutExcel($request, $user, $isAdmin);
    }

    private function exportTindakLanjutExcel($request, $user, $isAdmin)
    {
        $query = TindakLanjut::with([
            'arahan.keputusan',
            'arahan.unitKerja',
            'unitKerja',
            'creator',
            'approvals.approver',
        ]);

        $data = $query->latest()->get();
        $roleName = $user->getRoleNames()->first();
        $filename = 'tindaklanjut_' . str_replace(' ', '_', $roleName) . '_' . now()->format('Ymd_His') . '.xlsx';

        return Excel::download(new TindakLanjutExport($data, $isAdmin, $user), $filename);
    }

    private function exportTindakLanjut($request, $user, $isAdmin)
    {
        $query = TindakLanjut::with([
            'arahan.keputusan',
            'arahan.unitKerja',
            'unitKerja',
            'creator',
            'approvals.approver',
        ]);

        if ($isAdmin) {
            if ($request->filled('unit_kerja_id')) {
                $query->where('unit_kerja_id', $request->unit_kerja_id);
            }
        } elseif ($user->hasRole('Atasan Auditi')) {
            $subordinateUnitIds = $user->subordinates->pluck('unit_kerja_id')->unique();
            $query->whereIn('unit_kerja_id', $subordinateUnitIds);
            if ($request->filled('unit_kerja_id')) {
                $query->where('unit_kerja_id', $request->unit_kerja_id);
            }
        } else {
            $query->where('unit_kerja_id', $user->unit_kerja_id)
                ->where('created_by', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('keputusan_id')) {
            $query->whereHas('arahan', fn($q) => $q->where('keputusan_id', $request->keputusan_id));
        }

        if ($request->filled('periode_bulan')) {
            $query->where('periode_bulan', $request->periode_bulan);
        }

        if ($request->filled('periode_tahun')) {
            $query->where('periode_tahun', $request->periode_tahun);
        }

        $data     = $query->latest()->get();
        $roleName = $user->getRoleNames()->first();
        $filename = 'tindaklanjut_' . str_replace(' ', '_', $roleName) . '_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($data, $isAdmin, $user) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            if ($isAdmin) {
                fputcsv($file, [
                    'Periode Keputusan',
                    'Unit Kerja',
                    'Arahan/Strategi',
                    'Bulan/Tahun Laporan',
                    'Uraian Tindak Lanjut',
                    'Kendala',
                    'Status',
                    'Progress Stage',
                    'Dibuat Oleh',
                    'Tanggal Input',
                    'Catatan Approver Terakhir',
                ]);
            } elseif ($user->hasRole('Atasan Auditi')) {
                fputcsv($file, [
                    'Periode Keputusan',
                    'Unit Kerja',
                    'Arahan/Strategi',
                    'Bulan/Tahun Laporan',
                    'Uraian Tindak Lanjut',
                    'Kendala',
                    'Status',
                    'Dibuat Oleh',
                    'Tanggal Input',
                ]);
            } else {
                fputcsv($file, [
                    'Periode Keputusan',
                    'Arahan/Strategi',
                    'Bulan/Tahun Laporan',
                    'Uraian Tindak Lanjut',
                    'Kendala',
                    'Status',
                    'Tanggal Input',
                ]);
            }

            $statusLabel = [
                'pending'     => 'Menunggu Approval',
                'in_approval' => 'Sedang DiApproval',
                'approved'    => 'Selesai',
                'rejected'    => 'Revisi / TD',
            ];


            foreach ($data as $i => $tl) {
                $approvedStages  = $tl->approvals->where('status', 'approved')->count();
                $lastNote        = $tl->approvals->whereNotNull('note')->sortByDesc('updated_at')->first();
                $status          = $statusLabel[$tl->status] ?? $tl->status;
                $periodelaporan  = $tl->periode_bulan . '/' . $tl->periode_tahun;
                $strategi        = $tl->arahan->strategi ?? '-';
                $unitKerja       = $tl->unitKerja->name ?? '-';
                $periode         = $tl->arahan->keputusan->periode_year ?? '-';
                $createdBy       = $tl->creator->name ?? '-';
                $tanggalInput    = $tl->created_at->format('d/m/Y H:i');
                $catatanApprover = $lastNote->note ?? '-';

                if ($isAdmin) {
                    fputcsv($file, [
                        $i + 1,
                        $periode,
                        $unitKerja,
                        $strategi,
                        $periodelaporan,
                        $tl->tindak_lanjut,
                        $tl->kendala ?? '-',
                        $status,
                        "Stage {$approvedStages}/5",
                        $createdBy,
                        $tanggalInput,
                        $catatanApprover,
                    ]);
                } elseif ($user->hasRole('Atasan Auditi')) {
                    fputcsv($file, [
                        $i + 1,
                        $unitKerja,
                        $strategi,
                        $periodelaporan,
                        $tl->tindak_lanjut,
                        $tl->kendala ?? '-',
                        $status,
                        $createdBy,
                        $tanggalInput,
                    ]);
                } else {
                    fputcsv($file, [
                        $i + 1,
                        $strategi,
                        $periodelaporan,
                        $tl->tindak_lanjut,
                        $tl->kendala ?? '-',
                        $status,
                        $tanggalInput,
                    ]);
                }
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportKeputusan($request, $user, $isAdmin)
    {
        $query = Keputusan::with(['creator', 'arahan.unitKerja']);

        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $query->whereHas('arahan', fn($q) => $q->where('unit_kerja_id', $user->unit_kerja_id));
        }

        if ($request->filled('status')) {
            $status = $request->status;

            if ($status === 'revisi') {
                $query->where('status', 'rejected')
                    ->whereHas('arahan.keputusan', fn($q) => $q->where('status', '!=', 'TD'));
            } elseif ($status === 'td') {
                $query->where('status', 'rejected')
                    ->whereHas('arahan.keputusan', fn($q) => $q->where('status', 'TD'));
            } else {
                $query->where('status', $status);
            }
        }

        if ($request->filled('periode_tahun')) {
            $query->where('periode_year', $request->periode_tahun);
        }

        $data     = $query->latest()->get();
        $filename = 'keputusan_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        $callback = function () use ($data) {
            $file = fopen('php://output', 'w');
            fprintf($file, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($file, [
                'No',
                'Periode',
                'Status',
                'Jumlah Arahan',
                'Dibuat Oleh',
                'Tanggal Dibuat',
            ]);

            $statusLabel = [
                'BD' => 'Belum Dikirim',
                'BS' => 'Belum Selesai',
                'S'  => 'Selesai',
                'TD' => 'Tidak Dapat Ditindaklanjuti',
            ];

            foreach ($data as $i => $kep) {
                fputcsv($file, [
                    $i + 1,
                    $kep->periode_year,
                    $statusLabel[$kep->status] ?? $kep->status,
                    $kep->arahan->count(),
                    $kep->creator->name ?? '-',
                    $kep->created_at->format('d/m/Y'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
