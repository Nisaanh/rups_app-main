<?php

namespace App\Http\Controllers;

use App\Models\Bidang;
use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BidangController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->get('search');
        
        $bidang = Bidang::when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(15);
        
        $stats = [
            'total' => Bidang::count(),
            'total_unit_terkait' => 0, // Set 0 karena global/tidak terikat kaku
            'total_arahan' => \App\Models\Arahan::count(),
        ];

        return view('bidang.index', compact('bidang', 'stats'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:bidang,name',
        ]);

        try {
            Bidang::create(['name' => $request->name]);
            return redirect()->back()->with('success', 'Bidang berhasil ditambahkan');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Bidang $bidang)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:bidang,name,' . $bidang->id,
        ]);

        try {
            $bidang->update(['name' => $request->name]);
            return redirect()->back()->with('success', 'Bidang berhasil diupdate');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal: ' . $e->getMessage());
        }
    }

    public function destroy(Bidang $bidang)
    {
        try {
            $bidang->delete();
            return redirect()->back()->with('success', 'Bidang berhasil dihapus');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus: ' . $e->getMessage());
        }
    }

}