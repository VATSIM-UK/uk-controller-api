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
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->string('callsign')
                ->index()
                ->nullable()
                ->after('destination')
                ->comment('An exact match on the non-airline part of thecallsign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->dropColumn('callsign');
        });
    }
};
