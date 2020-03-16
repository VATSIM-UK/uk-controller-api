<?php

use App\Models\Airfield\Airfield;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Carbon;

class AddMissingAirfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        foreach ($this->getAirfieldData() as $airfieldDatum) {
            Airfield::insert($airfieldDatum);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        foreach ($this->getAirfieldData() as $airfieldDatum) {
            Airfield::where('code', $airfieldDatum['code'])->firstOrFail()->delete();
        }
    }

    private function getAirfieldData() : array
    {
        return [
            [
                'code' => 'EGWU',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => '{"type": "direct", "airfield": "EGLL"}',
                'created_at' => Carbon::now(),
            ],
            [
                'code' => 'EGPD',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => '{"type": "direct", "airfield": "EGPD"}',
                'created_at' => Carbon::now(),
            ],
        ];
    }
}
