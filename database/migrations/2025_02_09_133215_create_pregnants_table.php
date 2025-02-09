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
        Schema::create('pregnants', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('posyandu_id');
            $table->string('name');
            $table->string('nik');
            $table->string('alamat');
            $table->string('awal_kehamilan');
            $table->string('perkiraan_hamil');
            $table->string('nama_suami');
            $table->timestamps();

            $table->foreign('posyandu_id')->references('id')->on('posyandus')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnants');
    }
};
