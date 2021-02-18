<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;

class AddDeemedSeparatedHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $willo = DB::table('holds')
            ->join('navaids', 'holds.navaid_id', '=', 'navaids.id')
            ->where('navaids.identifier', 'WILLO')
            ->first()
            ->id;

        $timba = DB::table('holds')
            ->join('navaids', 'holds.navaid_id', '=', 'navaids.id')
            ->where('navaids.identifier', 'TIMBA')
            ->first()
            ->id;

        DB::table('deemed_separated_holds')->insert(
            [
                [
                    'first_hold_id' => $willo,
                    'second_hold_id' => $timba,
                    'vsl_insert_distance' => 6,
                ],
                [
                    'first_hold_id' => $timba,
                    'second_hold_id' => $willo,
                    'vsl_insert_distance' => 6,
                ],
            ]
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDs');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
