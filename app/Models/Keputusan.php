<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Keputusan extends Model
{
    protected $table = 'keputusan';
    use HasFactory;

    protected $fillable = ['nomor_keputusan','periode_year', 'status', 'created_by'];

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
        $arahanList = $this->arahan;
        
        if ($arahanList->isEmpty()) {
            $this->update(['status' => 'BD']);
            return;
        }
        
        $statuses = [];
        foreach ($arahanList as $arahan) {
            // Refresh relasi arahan untuk mendapatkan data terbaru
            $arahan->load('tindakLanjut');
            $statuses[] = $arahan->getAggregateStatus();
        }
        
        // Cek apakah semua arahan Selesai (S)
        $allSelesai = collect($statuses)->every(fn($s) => $s === 'S');
        if ($allSelesai) {
            $this->update(['status' => 'S']);
            return;
        }
        
        // Cek apakah semua arahan TD
        $allTd = collect($statuses)->every(fn($s) => $s === 'td');
        if ($allTd) {
            $this->update(['status' => 'td']);
            return;
        }
        
        // Cek apakah ada arahan yang masih BS atau belum selesai
        $hasBs = collect($statuses)->contains(fn($s) => in_array($s, ['BS', 'BD', 'pending', 'in_approval']));
        if ($hasBs) {
            $this->update(['status' => 'BS']);
            return;
        }
        
        // Default
        $this->update(['status' => 'BS']);
    }
}