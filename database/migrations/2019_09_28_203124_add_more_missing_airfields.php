<?php

use App\Models\Airfield\Airfield;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class AddMoreMissingAirfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Airfield::insert($this->getAirfieldData());

        // EGLF is special as it got introduced midway through the work, so skip it if it's already there
        Airfield::firstOrCreate(
            [
                'code' => 'EGLF',
            ],
            [
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGLF"}',
                'created_at' => Carbon::now(),
            ],
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->getAirfieldData() as $airfieldDatum) {
            $airfield = Airfield::where('code', $airfieldDatum['code'])->first();
            if ($airfield) {
                $airfield->delete();
            }
        }
    }

    private function getAirfieldData() : array
    {
        return [
            [
                'code' => 'EGAA',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGAA"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGAC',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGAA"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGAE',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGAE"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGBE',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGBE"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGBJ',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGBJ"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGHQ',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGHQ"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGKA',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGKA"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGMD',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGMD"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGNC',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGNC"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGNH',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGNH"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGNJ',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGNJ"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGNR',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGNR"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGNV',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGNV"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPA',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPA"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPB',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPB"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPC',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPC"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPE',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPE"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPM',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPM"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPN',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPN"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPO',
                'transition_altitude' => 3000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPO"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGSC',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGSC"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGTC',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGTC"}',
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
