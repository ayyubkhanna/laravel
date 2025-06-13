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
        Schema::table('child_checkups', function (Blueprint $table) {
            $table->integer('age')->after('child_id');
            $table->boolean('stunting')->after('weight');
            $table->json('imunisasi')->after('stunting');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('child_checkups', function (Blueprint $table) {
            //
        });
    }
};
