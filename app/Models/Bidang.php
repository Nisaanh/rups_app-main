<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bidang extends Model
{
    protected $table = 'bidang';

    protected $fillable = [
        'name'
    ];

    public function arahan() 
{
    return $this->hasMany(Arahan::class, 'bidang_id');
}
}