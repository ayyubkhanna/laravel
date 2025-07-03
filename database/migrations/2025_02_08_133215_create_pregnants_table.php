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
            $table->unsignedBigInteger('peopleId');
            $table->date('pregnancyStartDate');
            $table->date('estimatedDueDate');
            $table->date('actualDeliveryDate')->nullable();
            $table->enum('status', ['aktif', 'melahirkan', 'selesai'])->default('aktif');
            $table->timestamps();

            $table->foreign('peopleId')->references('id')->on('people')->onDelete('cascade');
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
