<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keputusan extends Model
{
    protected $table = 'keputusan';
    use HasFactory;

    protected $fillable = ['periode_year', 'status', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function arahan() 
    {
        return $this->hasMany(Arahan::class, 'keputusan_id');
    }

    /**
     * Update status keputusan berdasarkan status agregat semua arahan
     */
    public function updateStatusBasedOnArahan()
{
    $allArahan = $this->arahan()->with('tindakLanjut')->get();

    if ($allArahan->isEmpty()) {
        return;
    }

    $allSelesai = $allArahan->every(function ($arahan) {
        $tindakLanjut = $arahan->tindakLanjut;

        // Arahan belum ada TL sama sekali → belum selesai
        if ($tindakLanjut->isEmpty()) {
            return false;
        }

        // Ambil TL terbaru per unit kerja
        $latestPerUnit = $tindakLanjut
            ->groupBy('unit_kerja_id')
            ->map(fn($list) => $list->sortByDesc('created_at')->first());

        // Semua unit harus approved atau td
        return $latestPerUnit->every(fn($tl) => in_array($tl->status, ['approved', 'td']));
    });

    if ($allSelesai) {
        $this->update(['status' => 'S']);
    } else {
        // Kalau ada yang balik rejected/pending, kembalikan ke BS (Aktif)
        if ($this->status === 'S') {
            $this->update(['status' => 'BS']);
        }
    }
}
}