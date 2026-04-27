<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Arahan extends Model
{
    protected $table = 'arahan';
    use HasFactory;

    protected $fillable = [
        'keputusan_id', 'bidang_id', 
        'tanggal_target', 'strategi', 'status'
    ];

    protected $casts = [
        'tanggal_target' => 'date',
    ];

    public function keputusan()
    {
        return $this->belongsTo(Keputusan::class);
    }

    public function bidang()
    {
        return $this->belongsTo(Bidang::class);
    }

    // Relasi many-to-many dengan User (PIC)
    public function pics()
    {
        return $this->belongsToMany(User::class, 'arahan_pic', 'arahan_id', 'user_id')
                    ->withTimestamps();
    }

public function unitKerja()
{
    return $this->belongsTo(UnitKerja::class, 'pic_unit_kerja_id');
}

    // Untuk backward compatibility
    public function pic()
    {
        return $this->belongsTo(User::class, 'pic_unit_kerja_id');
    }

    public function tindakLanjut()
    {
        return $this->hasMany(TindakLanjut::class);
    }

    /**
     * Mendapatkan status agregat arahan berdasarkan semua tindak lanjut dari berbagai unit
     */
    public function getAggregateStatus()
    {
        $tlPerUnit = $this->tindakLanjut->groupBy('unit_kerja_id');
        
        if ($tlPerUnit->isEmpty()) {
            return 'BD'; // Belum Ditindaklanjuti
        }
        
        $statuses = [];
        foreach ($tlPerUnit as $unitId => $tlList) {
            $latestTl = $tlList->sortByDesc('created_at')->first();
            $statuses[] = $latestTl->status;
        }
        
        // Cek apakah ada yang masih pending/in_approval
        $hasInProgress = collect($statuses)->contains(fn($s) => in_array($s, ['pending', 'in_approval']));
        if ($hasInProgress) {
            return 'BS'; // Belum Selesai
        }
        
        // Cek apakah semua unit sudah approved
        $allApproved = collect($statuses)->every(fn($s) => $s === 'approved');
        if ($allApproved) {
            return 'S'; // Selesai
        }
        
        // Cek apakah ada yang TD
        $hasTd = collect($statuses)->contains(fn($s) => $s === 'td');
        if ($hasTd) {
            return 'td'; // TD
        }
        
        // Cek apakah semua unit rejected
        $allRejected = collect($statuses)->every(fn($s) => $s === 'rejected');
        if ($allRejected) {
            return 'BS'; // Perlu Revisi
        }
        
        return 'BS';
    }
}