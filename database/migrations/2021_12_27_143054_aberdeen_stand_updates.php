<?php

use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Services\SectorfileService;
use Illuminate\Database\Migrations\Migration;

class AberdeenStandUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->setInternationalStands();
        $this->setDomesticStands();
        $this->setCargoStands();
        $this->addMissingStands();
        $this->setMaxAircraftTypes();
        $this->addAirlineAssignments();
        $this->removeClosedStands();
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

    private function setInternationalStands(): void
    {
        Stand::whereIn('identifier', ['1', '2', '3', '4', '5', '6'])
            ->airfield('EGPD')
            ->update(['type_id' => StandType::where('key', 'INTERNATIONAL')->firstOrFail()->id]);
    }

    private function setDomesticStands(): void
    {
        Stand::whereIn('identifier', ['7', '8', '10', '10R', '10L', '11', '12', '13', '14', '15', '19', '20'])
            ->airfield('EGPD')
            ->update(['type_id' => StandType::where('key', 'DOMESTIC')->firstOrFail()->id]);
    }

    private function setCargoStands(): void
    {
        Stand::whereIn('identifier', ['9', '10A'])
            ->airfield('EGPD')
            ->update(['type_id' => StandType::where('key', 'CARGO')->firstOrFail()->id]);
    }

    private function addMissingStands(): void
    {
        $aberdeen = Airfield::where('code', 'EGPD')->firstOrFail()->id;
        // 1A
        $coordinates1a = SectorfileService::coordinateFromNats('571157.26N', '0021206.18W');
        Stand::create(
            [
                'airfield_id' => $aberdeen,
                'identifier' => '1A',
                'latitude' => $coordinates1a->getLat(),
                'longitude' => $coordinates1a->getLng(),
                'type_id' => StandType::where('key', 'INTERNATIONAL')->firstOrFail()->id,
                'wake_category_id' => WakeCategory::where('code', 'UM')->firstOrFail()->id,
                'assignment_priority' => 100,
                'max_aircraft_id' => Aircraft::where('code', 'B763')->firstOrFail()->id,
            ]
        );

        // 7A
        $coordinates7a = SectorfileService::coordinateFromNats('571204.43N', '0021212.36W');
        Stand::create(
            [
                'airfield_id' => $aberdeen,
                'identifier' => '7A',
                'latitude' => $coordinates7a->getLat(),
                'longitude' => $coordinates7a->getLng(),
                'type_id' => StandType::where('key', 'DOMESTIC')->firstOrFail()->id,
                'wake_category_id' => WakeCategory::where('code', 'UM')->firstOrFail()->id,
                'assignment_priority' => 100,
                'max_aircraft_id' => Aircraft::where('code', 'B763')->firstOrFail()->id,
            ]
        );
    }

    private function setMaxAircraftTypes(): void
    {
        Stand::whereIn('identifier', ['1', '7'])
            ->airfield('EGPD')
            ->update(['max_aircraft_id' => Aircraft::where('code', 'B739')->firstOrFail()->id]);

        Stand::whereIn('identifier', ['1A'])
            ->airfield('EGPD')
            ->update(['max_aircraft_id' => Aircraft::where('code', 'B763')->firstOrFail()->id]);

        Stand::whereIn('identifier', ['2', '3', '4', '5', '6', ' 9'])
            ->airfield('EGPD')
            ->update(['max_aircraft_id' => Aircraft::where('code', 'B738')->firstOrFail()->id]);

        Stand::whereIn('identifier', ['8', '10', '11', '12', '13', '14'])
            ->airfield('EGPD')
            ->update(['max_aircraft_id' => Aircraft::where('code', 'E145')->firstOrFail()->id]);

        Stand::whereIn('identifier', ['15', '19'])
            ->airfield('EGPD')
            ->update(['max_aircraft_id' => Aircraft::where('code', 'SB20')->firstOrFail()->id]);

        Stand::whereIn('identifier', ['20'])
            ->airfield('EGPD')
            ->update(['max_aircraft_id' => Aircraft::where('code', 'JS41')->firstOrFail()->id]);
    }

    private function addAirlineAssignments(): void
    {
        Stand::whereIn('identifier', ['5', '6'])
            ->airfield('EGPD')
            ->get()
            ->each(function (Stand $stand) {
                $stand->airlines()->sync(Airline::where('icao_code', 'EZY')->firstOrFail());
            });

        Stand::whereIn('identifier', ['7'])
            ->airfield('EGPD')
            ->get()
            ->each(function (Stand $stand) {
                $stand->airlines()->sync(Airline::where('icao_code', 'BAW')->firstOrFail());
            });

        // Loganair / Eastern airways - set domestic and international stands
        $loganAir = Airline::where('icao_code', 'LOG')->firstOrFail()->id;
        $easternAirways = Airline::where('icao_code', 'EZE')->firstOrFail()->id;
        Stand::whereIn('identifier', ['8', '10', '11', '12', '13', '14'])
            ->airfield('EGPD')
            ->get()
            ->each(function (Stand $stand) use ($easternAirways, $loganAir) {
                $stand->airlines()->syncWithPivotValues([$loganAir, $easternAirways], ['destination' => 'EG']);
            });

        Stand::whereIn('identifier', ['1', '1A', '2', '3', '4', '5', '6'])
            ->airfield('EGPD')
            ->get()
            ->each(function (Stand $stand) use ($easternAirways, $loganAir) {
                $stand->airlines()->sync(
                    [
                        $loganAir,
                        $easternAirways,
                    ]
                );
            });
    }

    private function removeClosedStands()
    {
        Stand::where('identifier', '17')
            ->airfield('EGPD')
            ->delete();
    }
}
