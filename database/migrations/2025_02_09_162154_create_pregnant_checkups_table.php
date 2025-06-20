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
        Schema::create('pregnant_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregnantId');
            $table->date('date');
            $table->text('result');
            $table->string('notes');
            $table->string('medicine');
            $table->timestamps();

            $table->foreign('pregnantId')->references('id')->on('pregnants')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnant_checkups');
    }
};
