<?php

use App\Models\Squawk\AirfieldPairing\AirfieldPairingSquawkRange;
use App\Models\Squawk\Ccams\CcamsSquawkRange;
use App\Models\Squawk\Orcam\OrcamSquawkRange;
use App\Models\Squawk\UnitConspicuity\UnitConspicuitySquawkCode;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRangeGuest;
use Illuminate\Database\Seeder;

class SquawkRangeTablesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        OrcamSquawkRange::create(
            [
                'origin' => 'KJ',
                'first' => '0101',
                'last' => '0101',
            ]
        );

        UnitDiscreteSquawkRange::create(
            [
                'unit' => 'EGKK',
                'first' => '0202',
                'last' => '0202',
            ]
        );

        CcamsSquawkRange::create(
            [
                'first' => '0303',
                'last' => '0303',
            ]
        );

        AirfieldPairingSquawkRange::create([
            'first' => '1234',
            'last' => '2345',
            'origin' => 'LF',
            'destination' => 'EGP',
        ]);

        UnitDiscreteSquawkRange::create([
            'first' => '2342',
            'last' => '4252',
            'unit' => 'SCO',
            'rules' => null,
        ]);

        UnitDiscreteSquawkRangeGuest::create([
            'primary_unit' => 'LON',
            'guest_unit' => 'LTC',
        ]);

        UnitConspicuitySquawkCode::create([
            'code' => '7221',
            'unit' => 'SCO',
            'rules' => null,
        ]);
    }
}
