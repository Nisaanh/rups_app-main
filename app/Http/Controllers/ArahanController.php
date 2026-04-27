<?php

namespace App\Http\Controllers;

use App\Models\Arahan;
use App\Models\Keputusan;
use App\Models\User;
use App\Http\Requests\ArahanRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use App\Models\Bidang;

class ArahanController extends Controller
{
    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();
        $query = Arahan::with(['keputusan', 'pics', 'bidang']);

        if ($user->hasRole(['Auditi', 'Atasan Auditi'])) {
            $query->whereHas('pics', function($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        $arahan = $query->latest()->paginate(10);
        return view('arahan.index', compact('arahan'));
    }

    public function create(Request $request)
    { 
        if (!Gate::allows('create_arahan')) {
            abort(403);
        }

        $keputusanId = $request->get('keputusan_id');

        $keputusanSelected = null;
        if ($keputusanId) {
            $keputusanSelected = Keputusan::findOrFail($keputusanId);
        }

        $keputusan = Keputusan::whereIn('status', ['BD', 'BS'])->latest()->get();
        $bidang    = Bidang::orderBy('name')->get();
        $users     = User::with('unitKerja')->orderBy('name')->get();

        $existingArahan = collect();
        if ($keputusanId) {
            $existingArahan = Arahan::where('keputusan_id', $keputusanId)
                ->whereIn('status', ['draft', 'pending'])
                ->with(['pics', 'bidang'])
                ->latest()
                ->get();
        }

        return view('arahan.create', compact(
            'keputusan',
            'keputusanId',
            'keputusanSelected',
            'existingArahan',
            'bidang',
            'users'
        ));
    }

    public function store(ArahanRequest $request)
    {
        if (!Gate::allows('create_arahan')) {
            abort(403);
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();

            // Buat arahan
            $arahan = Arahan::create([
                'keputusan_id'      => $data['keputusan_id'],
                'bidang_id'         => $data['bidang_id'],
                'strategi'          => $data['strategi'],
                'tanggal_target'    => $data['tanggal_target'],
                'status'            => 'draft'
            ]);
            
            // Sync PIC (many-to-many)
            if (isset($data['pic_unit_kerja_ids']) && is_array($data['pic_unit_kerja_ids'])) {
                $arahan->pics()->sync($data['pic_unit_kerja_ids']);
            }

            DB::commit();

            if ($request->after_save === 'continue') {
                return redirect()->route('arahan.create', ['keputusan_id' => $arahan->keputusan_id])
                    ->with('success', 'Butir arahan berhasil ditambahkan.');
            }

            return redirect()->route('keputusan.show', $arahan->keputusan_id)
                ->with('success', 'Arahan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal menyimpan arahan: ' . $e->getMessage());
        }
    }

    public function show(Arahan $arahan)
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if (!$user->hasRole(['Admin', 'Tim Monitoring'])) {
            $hasAccess = $arahan->pics()->where('user_id', $user->id)->exists();
            if (!$hasAccess) {
                abort(403);
            }
        }

        $arahan->load(['keputusan', 'pics', 'bidang', 'tindakLanjut']);
        return view('arahan.show', compact('arahan'));
    }

    public function edit(Arahan $arahan)
    {
        if (!Gate::allows('edit_arahan')) {
            abort(403);
        }

        if ($arahan->status !== 'draft') {
            return redirect()->route('keputusan.show', $arahan->keputusan_id)
                ->with('error', 'Arahan yang sudah dikirim tidak dapat diubah.');
        }

        $keputusanSelected = $arahan->keputusan;
        $bidang = Bidang::orderBy('name')->get();
        $users  = User::with('unitKerja')->orderBy('name')->get();
        
        $selectedPics = $arahan->pics->pluck('id')->toArray();

        $existingArahan = Arahan::where('keputusan_id', $arahan->keputusan_id)
            ->where('id', '!=', $arahan->id)
            ->whereIn('status', ['draft', 'pending'])
            ->with(['pics', 'bidang'])
            ->latest()
            ->get();

        return view('arahan.edit', compact(
            'arahan',
            'keputusanSelected',
            'existingArahan',
            'bidang',
            'users',
            'selectedPics'
        ));
    }

    public function update(ArahanRequest $request, Arahan $arahan)
    {
        if (!Gate::allows('edit_arahan')) {
            abort(403);
        }

        if ($arahan->status !== 'draft') {
            return redirect()->route('keputusan.show', $arahan->keputusan_id)
                ->with('error', 'Arahan yang sudah dikirim tidak dapat diubah.');
        }

        DB::beginTransaction();
        try {
            $data = $request->validated();
            
            $arahan->update([
                'bidang_id' => $data['bidang_id'],
                'strategi' => $data['strategi'],
                'tanggal_target' => $data['tanggal_target'],
            ]);
            
            if (isset($data['pic_unit_kerja_ids'])) {
                $arahan->pics()->sync($data['pic_unit_kerja_ids']);
            }
            
            DB::commit();

            if ($request->after_save === 'continue') {
                return redirect()->route('arahan.create', ['keputusan_id' => $arahan->keputusan_id])
                    ->with('success', 'Arahan berhasil diperbarui.');
            }

            return redirect()->route('keputusan.show', $arahan->keputusan_id)
                ->with('success', 'Arahan berhasil diperbarui.');
                
        } catch (\Exception $e) {
            DB::rollback();
            return back()->withInput()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy(Arahan $arahan)
    {
        if (!Gate::allows('delete_arahan')) {
            abort(403);
        }

        $keputusanId = $arahan->keputusan_id;
        
        // Hapus relasi many-to-many terlebih dahulu
        $arahan->pics()->detach();
        $arahan->delete();

        return redirect()->back()->with('success', 'Arahan berhasil dihapus');
    }
}