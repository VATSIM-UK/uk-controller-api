<?php

use App\Models\Airfield\Airfield;
use App\Models\Tma;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTmas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tmas = [
            [
                'name' => 'LTMA',
                'description' => 'London TMA',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_airfield_id' => Airfield::where('code', 'EGLL')->firstOrFail()->id,
            ],
            [
                'name' => 'MTMA',
                'description' => 'Manchester TMA',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_airfield_id' => Airfield::where('code', 'EGCC')->firstOrFail()->id,
            ],
            [
                'name' => 'STMA',
                'description' => 'Scottish TMA',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_airfield_id' => Airfield::where('code', 'EGPF')->firstOrFail()->id,
            ],
        ];

        Tma::insert($tmas);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $tmas = Tma::all();
        foreach ($tmas as $tma) {
            $tma->delete();
        }
    }
}
