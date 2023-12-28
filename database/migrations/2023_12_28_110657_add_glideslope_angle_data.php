<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('runways')
            ->update([
                'glideslope_angle' => 3,
            ]);

        // EGLC is 5.5
        DB::table('runways')
            ->join('airfield', 'runways.airfield_id', '=', 'airfield.id')
            ->where('airfield.code', 'EGLC')
            ->update([
                'glideslope_angle' => 5.5,
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
