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
        Schema::create('children', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('peopleId');
            $table->unsignedBigInteger('motherId');
            $table->string('numberKia')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->timestamps();

            $table->foreign('motherId')->references('id')->on('pregnants')->onDelete('cascade');
            $table->foreign('peopleId')->references('id')->on('people')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('children');
    }
};
