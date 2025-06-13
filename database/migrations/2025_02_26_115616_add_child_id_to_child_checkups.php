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
            $table->unsignedBigInteger('child_id')->after('id');

            $table->foreign('child_id')->on('children')->references('id')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('child_checkups', function (Blueprint $table) {
            $table->dropColumn('child_id');
        });
    }
};
