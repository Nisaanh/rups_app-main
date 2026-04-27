<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('arahan', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu jika ada
            try {
                $table->dropForeign(['pic_unit_kerja_id']);
            } catch (\Exception $e) {
                // Foreign key mungkin tidak ada
            }
            
            // Hapus kolom pic_unit_kerja_id
            $table->dropColumn('pic_unit_kerja_id');
        });
    }

    public function down()
    {
        Schema::table('arahan', function (Blueprint $table) {
            $table->unsignedBigInteger('pic_unit_kerja_id')->nullable()->after('bidang_id');
            $table->foreign('pic_unit_kerja_id')->references('id')->on('users')->onDelete('set null');
        });
    }
};