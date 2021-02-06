<?php

use App\Services\DependencyService;
use App\Services\PrenoteService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewGatwickSids extends Migration
{
    private const SIDS_TO_ADD = [
        'CLN1M' => 'FRANE1M',
        'CLN1V' => 'FRANE1V',
        'CLN5P' => 'FRANE1P',
        'CLN5W' => 'FRANE1W',
        'DVR1M' => 'MIMFO1M',
        'DVR1V' => 'MIMFO1V',
    ];
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add the sids, basing each off of what is currently there
        $gatwick = $this->getGatwickAirportId();
        $sidsToAdd = DB::table('sid')->where('airfield_id', $gatwick)
            ->whereIn('identifier', array_keys(self::SIDS_TO_ADD))
            ->get()
            ->map(function ($sid) {
                return [
                    'airfield_id' => $sid->airfield_id,
                    'identifier' => self::SIDS_TO_ADD[$sid->identifier],
                    'initial_altitude' => $sid->initial_altitude,
                    'handoff_id' => $sid->handoff_id,
                    'created_at' => Carbon::now(),
                ];
            })->toArray();

        DB::table('sid')->insert($sidsToAdd);

        // As BIG SIDs are on the way out, add an extra prenote to cover the old pairing to F_APP
        PrenoteService::createNewAirfieldPairingFromPrenote('EGKK', 'EGLL', 'EGKK_SID_BIG_APP');

        // Dependency update
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_PRENOTE');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $gatwick = $this->getGatwickAirportId();
        DB::table('sid')->where('airfield_id', $gatwick)
            ->whereIn('identifier', array_values(self::SIDS_TO_ADD))
            ->delete();

        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
    }

    private function getGatwickAirportId(): int
    {
        return DB::table('airfield')->where('code', 'EGKK')->first()->id;
    }
}
