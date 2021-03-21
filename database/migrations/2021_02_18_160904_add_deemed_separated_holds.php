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
        $willo = $this->getHoldId('WILLO');
        $timba = $this->getHoldId('TIMBA');
        $lambourne = $this->getHoldId('LAM');
        $biggin = $this->getHoldId('BIG');
        $ockham = $this->getHoldId('OCK');
        $bovvingdon = $this->getHoldId('BNN');
        $mirsi = $this->getHoldId('MIRSI');
        $rosun = $this->getHoldId('ROSUN');
        $dayne = $this->getHoldId('DAYNE');

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
                [
                    'first_hold_id' => $lambourne,
                    'second_hold_id' => $biggin,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $lambourne,
                    'second_hold_id' => $ockham,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $lambourne,
                    'second_hold_id' => $bovvingdon,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $biggin,
                    'second_hold_id' => $ockham,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $biggin,
                    'second_hold_id' => $lambourne,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $biggin,
                    'second_hold_id' => $bovvingdon,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $ockham,
                    'second_hold_id' => $biggin,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $ockham,
                    'second_hold_id' => $lambourne,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $ockham,
                    'second_hold_id' => $bovvingdon,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $bovvingdon,
                    'second_hold_id' => $biggin,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $bovvingdon,
                    'second_hold_id' => $lambourne,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $bovvingdon,
                    'second_hold_id' => $ockham,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $dayne,
                    'second_hold_id' => $mirsi,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $dayne,
                    'second_hold_id' => $rosun,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $rosun,
                    'second_hold_id' => $dayne,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $rosun,
                    'second_hold_id' => $mirsi,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $mirsi,
                    'second_hold_id' => $rosun,
                    'vsl_inser_distance' => 6,
                ],
                [
                    'first_hold_id' => $mirsi,
                    'second_hold_id' => $dayne,
                    'vsl_inser_distance' => 6,
                ],
            ]
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
    }

    private function getHoldId(string $fix): int
    {
        return DB::table('holds')
            ->join('navaids', 'holds.navaid_id', '=', 'navaids.id')
            ->where('navaids.identifier', $fix)
            ->first()
            ->id;
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
