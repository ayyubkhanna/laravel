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
        Schema::create('pregnant_information', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregnantId');
            $table->string('husbandName')->nullable();
            $table->integer('numberPhone')->nullable();
            $table->string('religion');
            $table->string('job');
            $table->string('image')->nullable();
            $table->timestamps();

            $table->foreign('pregnantId')->on('pregnants')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pregnant_information');
    }
};
