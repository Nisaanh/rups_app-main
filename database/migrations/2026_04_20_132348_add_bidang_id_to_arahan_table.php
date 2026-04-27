<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::table('arahan', function (Blueprint $table) {
    $table->foreignId('bidang_id')->nullable()->constrained('bidang')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('arahan', function (Blueprint $table) {
            //
        });
    }
};
