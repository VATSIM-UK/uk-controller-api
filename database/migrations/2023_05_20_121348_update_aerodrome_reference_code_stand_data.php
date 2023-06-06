<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Give every stand a rough aerodrome reference code
        $wakeCategoryToAerodromeReferenceCode = [
            'L' => 'A',
            'S' => 'B',
            'LM' => 'C',
            'UM' => 'D',
            'H' => 'E',
            'J' => 'F',
        ];
        foreach ($wakeCategoryToAerodromeReferenceCode as $wakeCategory => $aerodromeReference) {
            DB::table('stands')
                ->join('wake_categories', 'wake_categories.id', '=', 'stands.wake_category_id')
                ->where('wake_categories.code', $wakeCategory)
                ->update(['aerodrome_reference_code' => $aerodromeReference]);
        }

        // For stands with a specific aircraft type, update the aerodrome reference code to that of the aircraft
        DB::table('stands')
            ->join('aircraft', 'aircraft.id', '=', 'stands.max_aircraft_id')
            ->update(['stands.aerodrome_reference_code' => DB::raw('aircraft.aerodrome_reference_code')]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
