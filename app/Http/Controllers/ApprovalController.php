<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjut;
use App\Models\Approval;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    /**
     * Cek stage berdasarkan permission user
     */
    private function getCurrentStage(): ?int
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        for ($stage = 1; $stage <= 5; $stage++) {
            if ($user->can("approve_stage_{$stage}")) {
                return $stage;
            }
        }

        return null;
    }

    /**
     * Mendapatkan nama stage
     */
    private function getStageName(int $stage): string
    {
        $map = [
            1 => 'Atasan Auditi',
            2 => 'Tim Monitoring',
            3 => 'Pengendali Teknis',
            4 => 'Pengendali Mutu',
            5 => 'Penanggung Jawab',
        ];

        return $map[$stage] ?? '';
    }

    /**
     * Mendapatkan unit kerja yang bisa diakses user untuk approval
     * - Atasan Auditi (stage 1): hanya unit kerjanya sendiri + unit bawahan
     * - Stage 2-5: semua unit
     */
    private function getAccessibleUnitIds(): ?array
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $currentStage = $this->getCurrentStage();

        // Stage 2-5: bisa akses semua unit (return null berarti no filter)
        if ($currentStage !== 1) {
            return null;
        }

        // Stage 1 (Atasan Auditi): unit sendiri + unit bawahan
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

    /**
     * Filter approval berdasarkan akses user
     */
    private function filterApprovalsByAccess($query)
    {
        $accessibleUnitIds = $this->getAccessibleUnitIds();
        
        if ($accessibleUnitIds !== null) {
            $query->whereHas('tindakLanjut', function($q) use ($accessibleUnitIds) {
                $q->whereIn('unit_kerja_id', $accessibleUnitIds);
            });
        }
        
        return $query;
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user         = Auth::user();
        $roleName     = $user->getRoleNames()->first();
        $currentStage = $this->getCurrentStage();

        if ($currentStage) {
            $pendingApprovals = Approval::where('stage', $currentStage)
                ->where('status', 'pending')
                ->with([
                    'tindakLanjut.arahan.keputusan',
                    'tindakLanjut.unitKerja',
                    'tindakLanjut.creator',
                    'tindakLanjut.approvals',
                ]);
            
            // Terapkan filter akses
            $pendingApprovals = $this->filterApprovalsByAccess($pendingApprovals)
                ->latest()
                ->get();
        } else {
            $pendingApprovals = collect();
        }

        // Riwayat approval - juga difilter
        $approvalHistory = Approval::where('approved_by', $user->id)
            ->where('stage', $currentStage)
            ->with(['tindakLanjut.unitKerja', 'tindakLanjut.arahan']);
        
        $approvalHistory = $this->filterApprovalsByAccess($approvalHistory)
            ->latest()
            ->paginate(5);

        $pendingCount   = $pendingApprovals->count();
        $approvedCount  = Approval::where('approved_by', $user->id)->where('status', 'approved')->count();
        $rejectedCount  = Approval::where('approved_by', $user->id)->where('status', 'rejected')->count();
        $completedStage = $currentStage ? $currentStage - 1 : 0;

        return view('approval.index', compact(
            'pendingApprovals',
            'roleName',
            'currentStage',
            'completedStage',
            'pendingCount',
            'approvedCount',
            'rejectedCount',
            'approvalHistory'
        ));
    }

    public function approve(Request $request, $tindakLanjutId)
    {
        $request->validate([
            'note'    => 'nullable|string',
            'td_note' => 'nullable|string',
            'result'  => 'required|in:lanjut,selesai,td'
        ]);

        DB::beginTransaction();
        try {
            /** @var \App\Models\User $user */
            $user               = Auth::user();
            $roleName           = $user->getRoleNames()->first();
            $currentStageNumber = $this->getCurrentStage();

            if (!$currentStageNumber) {
                return back()->with('error', 'Anda tidak memiliki akses approval.');
            }

            $tindaklanjut = TindakLanjut::with(['arahan', 'unitKerja', 'arahan.keputusan'])
                ->findOrFail($tindakLanjutId);

            // === CEK AKSES UNTUK STAGE 1 (ATASAN AUDITI) ===
            if ($currentStageNumber === 1) {
                $accessibleUnitIds = $this->getAccessibleUnitIds();
                
                if (!$accessibleUnitIds || !in_array($tindaklanjut->unit_kerja_id, $accessibleUnitIds)) {
                    return back()->with('error', 'Anda hanya dapat mengapprove tindak lanjut dari unit kerja Anda sendiri atau bawahan Anda.');
                }
            }

            $approval = Approval::where('tindak_lanjut_id', $tindaklanjut->id)
                ->where('stage', $currentStageNumber)
                ->where('status', 'pending')
                ->first();

            if (!$approval) {
                return back()->with('error', 'Data approval tidak ditemukan atau sudah diproses.');
            }

            // ============================================================
            // KASUS 1: TD (Tidak Dapat Ditindaklanjuti)
            // ============================================================
            if ($request->result === 'td') {
                $approval->update([
                    'status'      => 'rejected',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'note'        => $request->td_note . ' (Ditetapkan sebagai TD)',
                ]);

                $tindaklanjut->update(['status' => 'td']);

                if ($tindaklanjut->arahan) {
                    $tindaklanjut->arahan->update(['status' => 'td']);

                    // Update status keputusan
                    $keputusan = $tindaklanjut->arahan->keputusan;
                    if ($keputusan) {
                        $keputusan->load('arahan.tindakLanjut');
                        $keputusan->updateStatusBasedOnArahan();
                    }
                }

                $tindaklanjut->approvals()->where('stage', '>', $currentStageNumber)->delete();

                DB::commit();
                return redirect()->route('approval.index')->with('success', 'Laporan ditetapkan sebagai TD. Proses dihentikan.');
            }

            // ============================================================
            // KASUS 2: APPROVE (Lanjut atau Selesai)
            // ============================================================
            $approval->update([
                'status'      => 'approved',
                'approved_by' => $user->id,
                'approved_at' => now(),
                'note'        => $request->note,
            ]);

            if ($currentStageNumber === 5 || $request->result === 'selesai') {
                $tindaklanjut->update(['status' => 'approved']);

                if ($tindaklanjut->arahan) {
                    // Cek apakah semua unit sudah approved
                    $allUnitsApproved = $tindaklanjut->arahan->tindakLanjut
                        ->groupBy('unit_kerja_id')
                        ->every(fn($tlList) => $tlList->sortByDesc('created_at')->first()->status === 'approved');

                    if ($allUnitsApproved) {
                        $tindaklanjut->arahan->update(['status' => 'S']);
                    }

                    // Update status keputusan
                    $keputusan = $tindaklanjut->arahan->keputusan;
                    if ($keputusan) {
                        $keputusan->load('arahan.tindakLanjut');
                        $keputusan->updateStatusBasedOnArahan();
                    }
                }
            } else {
                $tindaklanjut->update(['status' => 'in_approval']);
                $nextStage     = $currentStageNumber + 1;
                $nextStageName = $this->getStageName($nextStage);

                Approval::create([
                    'tindak_lanjut_id' => $tindaklanjut->id,
                    'stage'            => $nextStage,
                    'stage_name'       => $nextStageName,
                    'status'           => 'pending',
                ]);
            }

            DB::commit();
            return redirect()->route('approval.index')->with('success', 'Approval berhasil diproses.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function reject(Request $request, $tindakLanjutId)
    {
        $request->validate(['note' => 'required|string']);

        DB::beginTransaction();
        try {
            /** @var \App\Models\User $user */
            $user               = Auth::user();
            $roleName           = $user->getRoleNames()->first();
            $currentStageNumber = $this->getCurrentStage();

            if (!$currentStageNumber) {
                return back()->with('error', 'Anda tidak memiliki akses approval.');
            }

            $tindaklanjut = TindakLanjut::with(['arahan', 'unitKerja', 'arahan.keputusan'])
                ->findOrFail($tindakLanjutId);

            // === CEK AKSES UNTUK STAGE 1 (ATASAN AUDITI) ===
            if ($currentStageNumber === 1) {
                $accessibleUnitIds = $this->getAccessibleUnitIds();
                
                if (!$accessibleUnitIds || !in_array($tindaklanjut->unit_kerja_id, $accessibleUnitIds)) {
                    return back()->with('error', 'Anda hanya dapat merevisi tindak lanjut dari unit kerja Anda sendiri atau bawahan Anda.');
                }
            }

            // Cari approval yang pending untuk stage ini
            $approval = Approval::where('tindak_lanjut_id', $tindaklanjut->id)
                ->where('stage', $currentStageNumber)
                ->where('status', 'pending')
                ->first();

            if (!$approval) {
                DB::rollback();
                return back()->with('error', 'Data approval tidak ditemukan atau sudah diproses.');
            }

            // Update approval menjadi rejected
            $approval->update([
                'status'      => 'rejected',
                'note'        => $request->note,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            // Update status tindak lanjut
            $tindaklanjut->update(['status' => 'rejected']);

            // Update status arahan jika ada
            if ($tindaklanjut->arahan) {
                // Update status keputusan
                if ($tindaklanjut->arahan->keputusan) {
                    $keputusan = $tindaklanjut->arahan->keputusan;
                    $keputusan->load('arahan.tindakLanjut');
                    $keputusan->updateStatusBasedOnArahan();
                }
            }

            // HAPUS approval stage berikutnya jika ada
            $tindaklanjut->approvals()->where('stage', '>', $currentStageNumber)->delete();

            DB::commit();

            return redirect()->route('approval.index')->with('success', 'Laporan telah dikembalikan untuk revisi.');
        } catch (\Exception $e) {
            DB::rollback();
            \Log::error('Reject error: ' . $e->getMessage());
            return back()->with('error', 'Gagal memproses revisi: ' . $e->getMessage());
        }
    }

    public function show(TindakLanjut $tindaklanjut)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $tindakLanjut = $tindaklanjut->load([
            'unitKerja',
            'creator',
            'approvals.approver',
        ]);

        $currentStage = $this->getCurrentStage();

        // === CEK AKSES UNTUK STAGE 1 ===
        if ($currentStage === 1) {
            $accessibleUnitIds = $this->getAccessibleUnitIds();
            
            if (!$accessibleUnitIds || !in_array($tindakLanjut->unit_kerja_id, $accessibleUnitIds)) {
                abort(403, 'Anda tidak memiliki akses untuk melihat laporan unit ini.');
            }
        }

        $currentApproval = null;
        if ($currentStage) {
            $currentApproval = $tindakLanjut->approvals
                ->where('stage', $currentStage)
                ->where('status', 'pending')
                ->first();
        }

        return view('approval.show', compact('tindakLanjut', 'currentStage', 'currentApproval'));
    }
}