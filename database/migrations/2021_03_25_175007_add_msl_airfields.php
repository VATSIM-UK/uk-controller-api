<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMslAirfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airfields = DB::table('airfield')->get();
        $airfieldIdMap = $airfields->mapWithKeys(function ($airfield) {
            return [$airfield->code => $airfield->id];
        })->toArray();
        $airfields = $airfields->toArray();

        $mslAirfields = [];
        foreach ($airfields as $airfield) {
            $mslAirfieldArray = json_decode($airfield->msl_calculation, true);
            if (isset($mslAirfieldArray['airfield'])) {
                $mslAirfields[] = [
                    'airfield_id' => $airfield->id,
                    'msl_airfield_id' => $airfieldIdMap[$mslAirfieldArray['airfield']],
                ];
            } else {
                foreach ($mslAirfieldArray['airfields'] as $mslAirfield) {
                    $mslAirfields[] = [
                        'airfield_id' => $airfield->id,
                        'msl_airfield_id' => $airfieldIdMap[$mslAirfield],
                    ];
                }
            }
        }

        DB::table('msl_calculation_airfields')->insert($mslAirfields);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // If we rollback, table gets dropped
    }
}
