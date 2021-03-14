<?php

use App\Services\DependencyService;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddMoreDeemedSeparatedHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->makeHoldDeemedSeparated('WILLO', 'BIG', 6);
        $this->makeHoldDeemedSeparated('WILLO', 'OCK', 6);
        $this->makeHoldDeemedSeparated('WILLO', 'RUDMO', 6);
        $this->makeHoldDeemedSeparated('WILLO', 'TIGER', 6);
        $this->makeHoldDeemedSeparated('WILLO', 'ARNUN', 6);
        $this->makeHoldDeemedSeparated('ABBOT', 'LOREL', 6);
        $this->makeHoldDeemedSeparated('ABBOT', 'JACKO', 6);
        $this->makeHoldDeemedSeparated('ABBOT', 'SABER', 6);
        $this->makeHoldDeemedSeparated('ABBOT', 'BRASO', 6);
        $this->makeHoldDeemedSeparated('ABBOT', 'LAM', 6);
        $this->makeHoldDeemedSeparated('BIG', 'GODLU', 6);
        $this->makeHoldDeemedSeparated('BIG', 'RUDMO', 6);
        $this->makeHoldDeemedSeparated('BIG', 'TIGER', 6);
        $this->makeHoldDeemedSeparated('BIG', 'SABER', 6);
        $this->makeHoldDeemedSeparated('JACKO', 'GODLU', 6);
        $this->makeHoldDeemedSeparated('JACKO', 'LAM', 6);
        $this->makeHoldDeemedSeparated('GODLU', 'ABBOT', 6);
        $this->makeHoldDeemedSeparated('GODLU', 'LAM', 6);
        $this->makeHoldDeemedSeparated('GODLU', 'SABER', 6);
        $this->makeHoldDeemedSeparated('GODLU', 'TIGER', 6);
        $this->makeHoldDeemedSeparated('GODLU', 'TIMBA', 6);
        $this->makeHoldDeemedSeparated('GWC', 'OCK', 6);
        $this->makeHoldDeemedSeparated('LOREL', 'BNN', 6);
        $this->makeHoldDeemedSeparated('LOREL', 'LAM', 6);
        $this->makeHoldDeemedSeparated('KEGUN', 'TIPOD', 6);
        $this->makeHoldDeemedSeparated('MIRSI', 'TIPOD', 6);
        $this->makeHoldDeemedSeparated('ROKUP', 'PIGOT', 6);
        $this->makeHoldDeemedSeparated('ROKUP', 'DAYNE', 6);
        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
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

    private function makeHoldDeemedSeparated(string $firstHold, string $secondHold, int $vslInsertDistance)
    {
        $firstHoldId = $this->getHoldId($firstHold);
        $secondHoldId = $this->getHoldId($secondHold);

        DB::table('deemed_separated_holds')->insert(
            [
                [
                    'first_hold_id' => $firstHoldId,
                    'second_hold_id' => $secondHoldId,
                    'vsl_insert_distance' => $vslInsertDistance,
                ],
                [
                    'first_hold_id' => $secondHoldId,
                    'second_hold_id' => $firstHoldId,
                    'vsl_insert_distance' => $vslInsertDistance,
                ]
            ]
        );
    }

    private function getHoldId(string $holdIdentifier): int
    {
        return DB::table('holds')->where(
            'navaid_id',
            DB::table('navaids')->where('identifier', $holdIdentifier)->first()->id
        )->first()->id;
    }
}
