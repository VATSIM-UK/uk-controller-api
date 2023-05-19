<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    const WINGSPAN_UPDATES = [
        'IL62' => 140.0,
        'AT2P' => 100.0,
        'AT5P' => 100.0,
        'A337' => 210.0,
        'A338' => 210.0,
        'A339' => 208.9,
        'A400' => 139,
        'AN22' => 210,
        'A225' => 290,
        'AN24' => 96,
        'AN23' => 96,
        'B77L' => 23,
        'B778' => 235,
        'B779' => 130,
        'BCS1' => 130,
        'A221' => 115.2,
        'C5M' => 222,
        'C17' => 170,
        'DC10' => 155,
        'E290' => 110.6,
        'E295 ' => 110.6,
        'E175 ' => 101.71,
        'B701 ' => 130,
        'IL76 ' => 165,
        'IL86 ' => 157,
        'IL96 ' => 197,
        'MD11 ' => 170,
    ];

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update some wingspans so we have more data
        foreach (self::WINGSPAN_UPDATES as $aircraft => $wingspan) {
            DB::table('aircraft')
                ->where('code', $aircraft)
                ->update(['wingspan' => $wingspan]);
        }

        // Update the codes
        DB::table('aircraft')
            ->where('wingspan', '<', 49.2126)
            ->update(['aerodrome_reference_code' => 'A']);

        DB::table('aircraft')
            ->where('wingspan', '<', 78.7402)
            ->where('wingspan', '>=', 49.2126)
            ->update(['aerodrome_reference_code' => 'B']);

        DB::table('aircraft')
            ->where('wingspan', '<', 118.11)
            ->where('wingspan', '>=', 78.7402)
            ->update(['aerodrome_reference_code' => 'C']);

        DB::table('aircraft')
            ->where('wingspan', '<', 170.604)
            ->where('wingspan', '>=', 118.11)
            ->update(['aerodrome_reference_code' => 'D']);

        DB::table('aircraft')
            ->where('wingspan', '<', 213.255)
            ->where('wingspan', '>=', 170.604)
            ->update(['aerodrome_reference_code' => 'E']);

        DB::table('aircraft')
            ->where('wingspan', '>=', 213.255)
            ->update(['aerodrome_reference_code' => 'F']);

        // If there's any aircraft we haven't updated let's do a vague update based on wake category
        $wakeCategoryToAerodromeReferenceCode = [
            'L' => 'A',
            'S' => 'B',
            'LM' => 'C',
            'UM' => 'D',
            'H' => 'E',
            'J' => 'F',
        ];
        foreach ($wakeCategoryToAerodromeReferenceCode as $wakeCategory => $aerodromeReference) {
            DB::table('aircraft')
                ->join('aircraft_wake_category', 'aircraft_wake_category.aircraft_id', '=', 'aircraft.id')
                ->join('wake_categories', 'wake_categories.id', '=', 'aircraft_wake_category.wake_category_id')
                ->join(
                    'wake_category_schemes',
                    'wake_category_schemes.id',
                    '=',
                    'wake_categories.wake_category_scheme_id'
                )
                ->where('aerodrome_reference_code', 'A')
                ->where('wingspan', 0.0)
                ->where('wake_category_schemes.key', 'UK')
                ->where('wake_categories.code', $wakeCategory)
                ->update(['aerodrome_reference_code' => $aerodromeReference]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
