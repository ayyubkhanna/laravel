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
        Schema::create('prenetal_checkups', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pregnantId');
            $table->date('date');
            $table->integer('bodyWeight');
            $table->integer('bodyHeight');
            $table->integer('upperArm');
            $table->integer('abdominal');
            $table->integer('bloodPressure');
            $table->string('immunization')->nullable();
            $table->string('advice')->nullable();
            $table->timestamps();


            $table->foreign('pregnantId')->on('pregnants')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prenetal_checkups');
    }
};
