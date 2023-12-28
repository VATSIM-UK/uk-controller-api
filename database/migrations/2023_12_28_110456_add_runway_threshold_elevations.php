<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('runways')
            ->join('airfield', 'runways.airfield_id', '=', 'airfield.id')
            ->update([
                'runways.threshold_elevation' => DB::raw('airfield.elevation'),
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
