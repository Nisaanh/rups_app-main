<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TindakLanjut;
use App\Models\Arahan;
use App\Models\Approval;
use App\Models\Notification;
use App\Models\User;
use App\Http\Requests\TindakLanjutRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class TindakLanjutController extends Controller
{
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // HAPUS filter 'status', 'dikirim' - ambil SEMUA arahan
        $query = Arahan::with(['keputusan', 'bidang', 'pics', 'tindakLanjut'])
            ->whereIn('status', ['dikirim', 'td', 'S', 'BS']); // Ambil semua status

        // HANYA Auditi dan Atasan Auditi yang dibatasi melihat unit sendiri
        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $query->whereHas('pics', fn($q) => $q->where('users.id', $user->id));
        }

        if ($request->filled('search')) {
            $query->where('strategi', 'like', "%{$request->search}%");
        }

        $arahan = $query->latest()->paginate(15);

        // Hitung statistik tindak lanjut
        $tlQuery = TindakLanjut::query();
        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $tlQuery->whereHas('arahan', function ($q) use ($user) {
                $q->whereHas('pics', fn($sub) => $sub->where('users.id', $user->id));
            });
        }

        // HITUNG JUMLAH ARAHAN DENGAN STATUS TD
        $tdCount = (clone $query)->where('status', 'td')->count();

        // Hitung revisi (tindak lanjut dengan status rejected)
        $revisiCount = (clone $tlQuery)->where('status', 'rejected')->count();

        $stats = [
            'total'       => (clone $query)->count(),
            'pending'     => (clone $tlQuery)->where('status', 'pending')->count(),
            'in_approval' => (clone $tlQuery)->where('status', 'in_approval')->count(),
            'approved'    => (clone $tlQuery)->where('status', 'approved')->count(),
            'revisi'      => $revisiCount,
            'td'          => $tdCount, // Tambahkan statistik TD
        ];

        return view('tindaklanjut.index', compact('arahan', 'stats'));
    }

    public function create(Request $request)
{
    abort_if(!auth()->user()->can('create_tindak_lanjut'), 403);

    /** @var User $user */
    $user = Auth::user();
    $selectedArahanId = $request->get('arahan_id');

    $query = Arahan::query();

    // HANYA Auditi dan Atasan Auditi yang dibatasi
    if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
        $query->whereHas('pics', fn($q) => $q->where('users.id', $user->id));
    }

    $arahanList = $query->with(['keputusan', 'pics', 'bidang'])->latest()->get();

    // === TAMBAHKAN INI: Ambil data arahan yang dipilih ===
    $selectedArahan = null;
    if ($selectedArahanId) {
        $selectedArahan = $arahanList->firstWhere('id', $selectedArahanId);
        
        // Jika tidak ditemukan di $arahanList, cari langsung ke database
        if (!$selectedArahan) {
            $selectedArahan = Arahan::with(['keputusan', 'pics', 'bidang'])->find($selectedArahanId);
        }
    }
    // ================================================

    // Unit kerja yang bisa dipilih
    if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
        // Auditi & Atasan Auditi hanya bisa pilih unit kerjanya sendiri
        $unitKerja = collect([$user->unitKerja])->filter();
    } else {
        // Role lain bisa pilih semua unit kerja
        $unitKerja = \App\Models\UnitKerja::orderBy('name')->get();
    }

    $historiTindakLanjut = collect();
    if ($selectedArahanId) {
        $historiTindakLanjut = TindakLanjut::where('arahan_id', $selectedArahanId)
            ->with(['creator', 'unitKerja', 'approvals'])
            ->latest()
            ->get();

        // Filter histori berdasarkan unit kerja user (khusus Auditi & Atasan Auditi)
        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $historiTindakLanjut = $historiTindakLanjut->filter(function ($tl) use ($user) {
                return $tl->unit_kerja_id == $user->unit_kerja_id;
            });
        }
    }

    if ($arahanList->isEmpty()) {
        return redirect()->route('tindaklanjut.index')
            ->with('info', 'Tidak ada arahan yang tersedia untuk Anda.');
    }

    return view('tindaklanjut.create', compact(
        'arahanList',
        'selectedArahanId',
        'selectedArahan', // <-- TAMBAHKAN INI
        'historiTindakLanjut',
        'unitKerja'
    ));
}

    public function store(TindakLanjutRequest $request)
    {
        DB::beginTransaction();
        try {
            $data = $request->validated();
            $data['created_by'] = Auth::id();
            $data['status'] = 'pending';

            if ($request->hasFile('evidence')) {
                $data['evidence_url'] = $request->file('evidence')->store('evidences', 'public');
            }

            $tindakLanjut = TindakLanjut::create($data);

            // Hanya buat stage 1 — stage berikutnya dibuat berantai saat approve
            Approval::create([
                'tindak_lanjut_id' => $tindakLanjut->id,
                'stage'            => 1,
                'stage_name'       => 'Atasan Auditi',
                'status'           => 'pending'
            ]);

        // Notifikasi ke Atasan Auditi (picUnit dari pembuat laporan)
            /** @var User $currentUser */
            $currentUser = Auth::user();
            $atasanAuditi = $currentUser->picUnit;
            if ($atasanAuditi) {
                Notification::create([
                    'user_id' => $atasanAuditi->id,
                    'title'   => 'Approval Stage 1 - Atasan Auditi',
                    'message' => 'Tindak lanjut baru dari unit ' . $currentUser->unitKerja->name . ' membutuhkan persetujuan Anda.',
                    'type'    => 'approval',
                    'data'    => ['tindak_lanjut_id' => $tindakLanjut->id, 'stage' => 1]
                ]);
            }

            DB::commit();

            if ($request->create_another === 'yes') {
                return redirect()->route('tindaklanjut.create', ['arahan_id' => $tindakLanjut->arahan_id])
                    ->with('success', 'Tindak lanjut berhasil disimpan.');
            }

            return redirect()->route('tindaklanjut.index')
                ->with('success', 'Laporan berhasil dikirim ke Atasan Auditi untuk persetujuan.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', 'Gagal menyimpan: ' . $e->getMessage());
        }
    }

    public function showArahan($id)
{
    $user = Auth::user();

    $arahan = Arahan::with([
        'keputusan', 
        'tindakLanjut' => function($q) {
            $q->orderBy('created_at', 'desc'); // pastikan urutan desc
        },
        'tindakLanjut.creator', 
        'tindakLanjut.unitKerja', 
        'tindakLanjut.approvals.approver', 
        'pics', 
        'bidang'
    ])->findOrFail($id);

    $isPIC = $arahan->pics->contains('id', $user->id);
    $hasFullAccess = !$user->hasRole(['Auditi', 'Atasan Auditi']);

    if (!$hasFullAccess && !$isPIC) {
        abort(403, 'Anda tidak memiliki akses untuk melihat arahan ini.');
    }

    if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
        $arahan->tindakLanjut = $arahan->tindakLanjut->filter(function ($tl) use ($user) {
            return $tl->unit_kerja_id == $user->unit_kerja_id;
        });
    }

    // Group by unit, lalu ambil yang TERBARU per unit
    $tlPerUnit = $arahan->tindakLanjut
        ->groupBy('unit_kerja_id')
        ->map(function($tlList) {
            return $tlList->sortByDesc('created_at');
        });

    foreach ($arahan->tindakLanjut as $tl) {
        $tl->setRelation('approvals', $tl->approvals->sortBy('stage'));
    }

    $laporanTerakhir = $arahan->tindakLanjut->sortByDesc('created_at')->first();
    $currentProgress = $laporanTerakhir ? $laporanTerakhir->progres_persen : 0;

    return view('tindaklanjut.show_arahan', compact('arahan', 'currentProgress', 'tlPerUnit'));
}

    public function show(TindakLanjut $tindaklanjut)
    {
        $tindaklanjut->load(['arahan.keputusan', 'unitKerja', 'creator', 'approvals.approver']);
        return view('tindaklanjut.show', compact('tindaklanjut'));
    }

    public function edit(TindakLanjut $tindaklanjut)
    {
        $user = Auth::user();

        // Cek akses edit
        $hasFullAccess = !$user->hasRole(['Auditi', 'Atasan Auditi']);
        $isOwner = $tindaklanjut->created_by === $user->id;

        // Auditi & Atasan Auditi hanya bisa edit milik sendiri
        $canEdit = $hasFullAccess || $isOwner;

        if (!in_array($tindaklanjut->status, ['pending', 'rejected']) || !$canEdit) {
            return redirect()->route('tindaklanjut.index')
                ->with('error', 'Laporan ini tidak dapat diedit.');
        }

        // Ambil arahan yang tersedia
        $arahanQuery = Arahan::whereIn('status', ['dikirim', 'S', 'BS'])->with(['keputusan', 'pics']);

        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            // Auditi & Atasan Auditi hanya bisa pilih arahan yang menjadi PIC-nya
            $arahanQuery->whereHas('pics', fn($q) => $q->where('users.id', $user->id));
        }

        $arahanList = $arahanQuery->get();

        // Unit kerja yang bisa dipilih
        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $unitKerja = collect([$user->unitKerja])->filter();
        } else {
            $unitKerja = \App\Models\UnitKerja::orderBy('name')->get();
        }

        return view('tindaklanjut.edit', compact('tindaklanjut', 'arahanList', 'unitKerja'));
    }

    public function update(TindakLanjutRequest $request, TindakLanjut $tindaklanjut)
    {
        $user = Auth::user();

        $canEdit = $user->hasRole(['Admin', 'Tim Monitoring'])
            || $tindaklanjut->created_by === $user->id;

        if (!in_array($tindaklanjut->status, ['pending', 'rejected']) || !$canEdit) {
            return redirect()->route('tindaklanjut.index')
                ->with('error', 'Tidak dapat mengupdate tindak lanjut ini.');
        }

        try {
            $data = $request->validated();
            $data['status'] = 'pending';

            if ($request->hasFile('evidence')) {
                if ($tindaklanjut->evidence_url) {
                    Storage::disk('public')->delete($tindaklanjut->evidence_url);
                }
                $data['evidence_url'] = $request->file('evidence')->store('evidences', 'public');
            }

            $tindaklanjut->update($data);

            // HAPUS SEMUA APPROVAL LAMA (termasuk yang rejected)
            $tindaklanjut->approvals()->delete();

            // BUAT ULANG APPROVAL DARI STAGE 1
            Approval::create([
                'tindak_lanjut_id' => $tindaklanjut->id,
                'stage'            => 1,
                'stage_name'       => 'Atasan Auditi',
                'status'           => 'pending'
            ]);

        // NOTIFIKASI KE ATASAN AUDITI
            /** @var User $currentUser */
            $currentUser = Auth::user();
            $atasanAuditi = $currentUser->picUnit;
            if ($atasanAuditi) {
                Notification::create([
                    'user_id' => $atasanAuditi->id,
                    'title'   => 'Approval Stage 1 - Atasan Auditi (Revisi)',
                    'message' => 'Tindak lanjut hasil revisi dari unit ' . $currentUser->unitKerja->name . ' membutuhkan persetujuan Anda.',
                    'type'    => 'approval',
                    'data'    => ['tindak_lanjut_id' => $tindaklanjut->id, 'stage' => 1]
                ]);
            }

            return redirect()->route('tindaklanjut.show_arahan', $tindaklanjut->arahan_id)
                ->with('success', 'Tindak lanjut berhasil diperbarui dan dikembalikan ke antrian Approval Stage 1.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update: ' . $e->getMessage());
        }
    }

    public function destroy(TindakLanjut $tindaklanjut)
    {
        if ($tindaklanjut->status !== 'pending' || $tindaklanjut->created_by !== Auth::id()) {
            return redirect()->route('tindaklanjut.index')
                ->with('error', 'Tidak dapat menghapus tindak lanjut ini.');
        }

        try {
            if ($tindaklanjut->evidence_url) {
                Storage::disk('public')->delete($tindaklanjut->evidence_url);
            }

            $tindaklanjut->approvals()->delete();
            $tindaklanjut->delete();

            return redirect()->route('tindaklanjut.index')
                ->with('success', 'Tindak lanjut berhasil dihapus.');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }
}
