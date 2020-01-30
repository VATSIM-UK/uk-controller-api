<?php

use App\Models\Airfield\Airfield;
use Illuminate\Database\Migrations\Migration;

class AddAirfields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airfields = [
            // LTMA
            [
                'code' => 'EGKK',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGLL',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGLC',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGKB',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGSS',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGGW',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGTK',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],
            [
                'code' => 'EGMC',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGLL']),
            ],

            // MTMA
            [
                'code' => 'EGCC',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGCC']),
            ],
            [
                'code' => 'EGGP',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGCC']),
            ],
            [
                'code' => 'EGNM',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGCC']),
            ],
            [
                'code' => 'EGCN',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGCC']),
            ],

            // CICZ
            [
                'code' => 'EGJJ',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGJJ']),
            ],
            [
                'code' => 'EGJB',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGJJ']),
            ],
            [
                'code' => 'EGJA',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGJJ']),
            ],

            // SEVERN
            [
                'code' => 'EGGD',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'lowest', 'airfields' => ['EGGD', 'EGFF']]),
            ],
            [
                'code' => 'EGFF',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'lowest', 'airfields' => ['EGGD', 'EGFF']]),
            ],

            // SOLENT
            [
                'code' => 'EGHI',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGHI']),
            ],
            [
                'code' => 'EGHH',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGHH']),
            ],

            // MIDLANDS
            [
                'code' => 'EGBB',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGBB']),
            ],
            [
                'code' => 'EGNX',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGNX']),
            ],

            // STMA
            [
                'code' => 'EGPH',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGPF']),
            ],
            [
                'code' => 'EGPF',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGPF']),
            ],
            [
                'code' => 'EGPK',
                'transition_altitude' => 6000,
                'standard_high' => false,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGPF']),
            ],

            // MISC
            [
                'code' => 'EGTE',
                'transition_altitude' => 3000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGTE']),
            ],
            [
                'code' => 'EGNS',
                'transition_altitude' => 3000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGNS']),
            ],
            [
                'code' => 'EGSH',
                'transition_altitude' => 5000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGSH']),
            ],
            [
                'code' => 'EGNT',
                'transition_altitude' => 6000,
                'standard_high' => true,
                'msl_calculation' => json_encode(['type' => 'direct', 'airfield' => 'EGNT']),
            ],
        ];

        Airfield::insert($airfields);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $airfields = Airfield::all();
        foreach ($airfields as $airfield) {
            $airfield->delete();
        }
    }
}
