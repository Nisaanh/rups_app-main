<?php

namespace App\Http\Controllers;

use App\Models\Keputusan;
use App\Models\Notification;
use App\Http\Requests\KeputusanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\DB;
use App\Models\Arahan;

class KeputusanController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Keputusan::with('creator');

        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $query->whereHas('arahan', function ($q) use ($user) {
                $q->whereHas('pics', fn($p) => $p->where('users.id', $user->id));
            });
        }
        $keputusan = $query->latest()->paginate(10);
        return view('keputusan.index', compact('keputusan'));
    }

    public function create()
    {
        if (!Gate::allows('create_keputusan')) {
            abort(403);
        }

        $tahunSekarang = date('Y');
        $existing = Keputusan::where('periode_year', $tahunSekarang)->first();

        if ($existing) {
            return redirect()->route('arahan.create', ['keputusan_id' => $existing->id])
                ->with('info', 'Keputusan tahun ini sudah ada. Silakan tambah arahan.');
        }

        return view('keputusan.create');
    }

    public function store(KeputusanRequest $request)
    {
        if (!Gate::allows('create_keputusan')) {
            abort(403);
        }

        $exists = Keputusan::where('periode_year', $request->periode_year)->exists();

        if ($exists) {
            return redirect()->route('keputusan.index')
                ->with('error', 'Keputusan untuk tahun ini sudah ada. Silakan tambah arahan pada data yang tersedia.');
        }

        $keputusan = Keputusan::create([

            'periode_year'    => $request->periode_year,
            'status'          => 'BD',
            'created_by'      => Auth::id()
        ]);

        return redirect()->route('arahan.create', ['keputusan_id' => $keputusan->id])
            ->with('success', 'Keputusan berhasil dibuat. Silakan tambahkan arahan.');
    }



    /**
     * Method Baru: Finalisasi untuk mengirim semua arahan sekaligus
     */
    public function finalize($id)
    {
        $keputusan = Keputusan::with('arahan.pics')->findOrFail($id);

        DB::beginTransaction();
        try {
            $keputusan->update(['status' => 'BS']);

            // Update status arahan draft → dikirim (HARUS 'dikirim', bukan 'pending')
            Arahan::where('keputusan_id', $id)
                ->where('status', 'draft')
                ->update(['status' => 'dikirim']);
            $arahanList = $keputusan->arahan()->with('pics')->get();

            // Kirim notifikasi ke semua PIC via pivot
            foreach ($arahanList as $arahan) {
                foreach ($arahan->pics as $pic) {
                    Notification::create([
                        'user_id' => $pic->id,
                        'title'   => 'Arahan RUPS Baru',
                        'message' => "Anda menerima arahan baru}",
                        'type'    => 'arahan',
                        'data'    => json_encode(['arahan_id' => $arahan->id])
                    ]);
                }
            }

            DB::commit();

            return redirect()->route('keputusan.show', $id)
                ->with('success', 'Keputusan berhasil difinalisasi dan arahan telah dikirim.');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Gagal finalisasi: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $keputusan = Keputusan::with([
            'arahan.bidang',

            'arahan.tindakLanjut'
        ])->findOrFail($id);

        return view('keputusan.show', compact('keputusan'));
    }

    public function edit(Keputusan $keputusan)
    {
        if (!Gate::allows('edit_keputusan')) {
            abort(403);
        }

        if ($keputusan->status === 'BD') {
            return redirect()->route('arahan.create', ['keputusan_id' => $keputusan->id])
                ->with('info', 'Silakan lanjutkan pengisian butir arahan.');
        }
        return view('keputusan.edit', compact('keputusan'));
    }

    public function update(Request $request, Keputusan $keputusan)
    {
        $request->validate([
            'periode_year' => 'required|numeric',
        ]);

        $keputusan->update($request->all());

        return redirect()->route('keputusan.index')
            ->with('success', 'Data Keputusan berhasil diperbarui.');
    }

    public function destroy(Keputusan $keputusan)
    {
        if (!Gate::allows('delete_keputusan')) {
            abort(403);
        }
        $keputusan->delete();
        return redirect()->route('keputusan.index')->with('success', 'Keputusan berhasil dihapus');
    }
}
