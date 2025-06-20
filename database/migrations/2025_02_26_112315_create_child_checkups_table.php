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
        Schema::create('child_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('childId');
            $table->date('date');
            $table->integer('length_body');
            $table->integer('weight');
            $table->integer('age');
            $table->boolean('stunting');
            $table->json('imunisasi');
            $table->timestamps();

            $table->foreign('childId')->on('children')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('child_checkups');
    }
};
