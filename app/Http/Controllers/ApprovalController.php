<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjut;
use App\Models\Approval;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
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

    private function getNotificationTarget(int $stage): ?User
    {
        $stagePermission = "approve_stage_{$stage}";

        // Cari user aktif yang punya permission ini
        return User::where('status', 'active')
            ->whereHas('roles.permissions', fn($q) => $q->where('name', $stagePermission))
            ->orWhereHas('permissions', fn($q) => $q->where('name', $stagePermission))
            ->first();
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
                ])
                ->latest()
                ->get();
        } else {
            $pendingApprovals = collect();
        }

        $approvalHistory = Approval::where('approved_by', $user->id)
            ->where('stage', $currentStage)
            ->with(['tindakLanjut.unitKerja', 'tindakLanjut.arahan'])
            ->latest()
            ->paginate(10);

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
            'result'  => 'required|in:lanjut,rejected,selesai,td'
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

            $tindaklanjut = TindakLanjut::with(['arahan', 'unitKerja'])
                ->findOrFail($tindakLanjutId);

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
                    $keputusan->load('arahan.tindakLanjut'); // Refresh relasi
                    $keputusan->updateStatusBasedOnArahan();
                }

                $tindaklanjut->approvals()->where('stage', '>', $currentStageNumber)->delete();

                Notification::create([
                    'user_id' => $tindaklanjut->created_by,
                    'title'   => 'Laporan Ditetapkan TD',
                    'message' => "Laporan tindak lanjut unit {$tindaklanjut->unitKerja->name} ditetapkan TIDAK DAPAT DITINDAKLANJUTI oleh {$roleName}. Alasan: {$request->td_note}",
                    'type'    => 'td',
                    'data'    => ['tindak_lanjut_id' => $tindaklanjut->id],
                ]);

                DB::commit();
                return redirect()->route('approval.index')->with('success', 'Laporan ditetapkan sebagai TD. Proses dihentikan.');
            }

            // ============================================================
            // KASUS 2: REJECT (Revisi)
            // ============================================================
            if ($request->result === 'rejected') {
                $approval->update([
                    'status'      => 'rejected',
                    'approved_by' => $user->id,
                    'approved_at' => now(),
                    'note'        => $request->note,
                ]);

                $tindaklanjut->update(['status' => 'rejected']);

                if ($tindaklanjut->arahan) {
                    $tindaklanjut->arahan->update(['status' => 'BS']);
                    
                    // Update status keputusan
                    $keputusan = $tindaklanjut->arahan->keputusan;
                    $keputusan->load('arahan.tindakLanjut');
                    $keputusan->updateStatusBasedOnArahan();
                }

                $tindaklanjut->approvals()->where('stage', '>', $currentStageNumber)->delete();

                Notification::create([
                    'user_id' => $tindaklanjut->created_by,
                    'title'   => 'Laporan Perlu Revisi',
                    'message' => "Laporan unit {$tindaklanjut->unitKerja->name} dikembalikan oleh {$roleName}. Catatan: {$request->note}",
                    'type'    => 'revision',
                    'data'    => ['tindak_lanjut_id' => $tindaklanjut->id],
                ]);

                DB::commit();
                return redirect()->route('approval.index')->with('success', 'Laporan dikembalikan untuk revisi.');
            }

            // ============================================================
            // KASUS 3: APPROVE (Lanjut atau Selesai)
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
                    $keputusan->load('arahan.tindakLanjut');
                    $keputusan->updateStatusBasedOnArahan();
                }

                Notification::create([
                    'user_id' => $tindaklanjut->created_by,
                    'title'   => 'Laporan Selesai Disetujui',
                    'message' => 'Laporan tindak lanjut Anda telah disetujui oleh semua stage dan dinyatakan selesai.',
                    'type'    => 'approved',
                    'data'    => ['tindak_lanjut_id' => $tindaklanjut->id],
                ]);

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

                $nextApprover = $this->getNotificationTarget($nextStage);
                if ($nextApprover) {
                    Notification::create([
                        'user_id' => $nextApprover->id,
                        'title'   => "Approval Stage {$nextStage} - {$nextStageName}",
                        'message' => "Tindak lanjut dari unit {$tindaklanjut->unitKerja->name} telah disetujui stage sebelumnya dan menunggu persetujuan Anda.",
                        'type'    => 'approval',
                        'data'    => ['tindak_lanjut_id' => $tindaklanjut->id, 'stage' => $nextStage],
                    ]);
                }
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

            $tindaklanjut = TindakLanjut::with(['arahan', 'unitKerja'])
                ->findOrFail($tindakLanjutId);

            $approval = Approval::where('tindak_lanjut_id', $tindaklanjut->id)
                ->where('stage', $currentStageNumber)
                ->where('status', 'pending')
                ->first();

            if (!$approval) {
                return back()->with('error', 'Data approval tidak ditemukan atau sudah diproses.');
            }

            $approval->update([
                'status'      => 'rejected',
                'note'        => $request->note,
                'approved_by' => $user->id,
                'approved_at' => now(),
            ]);

            $tindaklanjut->update(['status' => 'rejected']);

            if ($tindaklanjut->arahan) {
                $tindaklanjut->arahan->update(['status' => 'BS']);
            }

            Notification::create([
                'user_id' => $tindaklanjut->created_by,
                'title'   => 'Laporan Perlu Revisi',
                'message' => "Laporan unit {$tindaklanjut->unitKerja->name} dikembalikan oleh {$roleName}. Catatan: {$request->note}",
                'type'    => 'revision',
                'data'    => ['tindak_lanjut_id' => $tindaklanjut->id],
            ]);

            DB::commit();
            return redirect()->route('approval.index')->with('success', 'Laporan telah dikembalikan untuk revisi.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal: ' . $e->getMessage());
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
