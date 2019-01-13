<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkRangeOwner;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLocalRanges extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $rangeInfo = [
            // Birmingham
            [
                'unit' => 'EGBB',
                'rules' => 'A',
                'start' => '0401',
                'stop' => '0420',
                'allow_duplicate' => false,
            ],

            // Exeter
            [
                'unit' => 'EGTE',
                'rules' => 'A',
                'start' => '0401',
                'stop' => '0450',
                'allow_duplicate' => false,
            ],

            // Edinburgh
            [
                'unit' => 'EGPH',
                'rules' => 'A',
                'start' => '0430',
                'stop' => '0437',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGPH',
                'rules' => 'A',
                'start' => '0441',
                'stop' => '0443',
                'allow_duplicate' => false,
            ],

            // Farnborough + LARS
            [
                'unit' => 'EGLF',
                'rules' => 'A',
                'start' => '0421',
                'stop' => '0427',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLF',
                'rules' => 'A',
                'start' => '0450',
                'stop' => '0456',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLF',
                'rules' => 'A',
                'start' => '0430',
                'stop' => '0446',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLF',
                'rules' => 'A',
                'start' => '0460',
                'stop' => '0465',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLF',
                'rules' => 'A',
                'start' => '0467',
                'stop' => '0467',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLF',
                'rules' => 'A',
                'start' => '1730',
                'stop' => '1746',
                'allow_duplicate' => false,
            ],

            // Newquay
            [
                'unit' => 'EGHQ',
                'rules' => 'A',
                'start' => '1730',
                'stop' => '1744',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGHQ',
                'rules' => 'A',
                'start' => '1750',
                'stop' => '1757',
                'allow_duplicate' => false,
            ],

            // Conningsby
            [
                'unit' => 'EGXC',
                'rules' => 'A',
                'start' => '1730',
                'stop' => '1756',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGXC',
                'rules' => 'A',
                'start' => '1760',
                'stop' => '1777',
                'allow_duplicate' => false,
            ],

            // Yeovilton
            [
                'unit' => 'EGDY',
                'rules' => 'A',
                'start' => '1760',
                'stop' => '1777',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGDY',
                'rules' => 'A',
                'start' => '4320',
                'stop' => '4327',
                'allow_duplicate' => false,
            ],

            // Glasgow
            [
                'unit' => 'EGPF',
                'rules' => 'A',
                'start' => '2601',
                'stop' => '2617',
                'allow_duplicate' => false,
            ],

            // Aberdeen
            [
                'unit' => 'EGPD',
                'rules' => 'A',
                'start' => '2621',
                'stop' => '2630',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGPD',
                'rules' => 'A',
                'start' => '2631',
                'stop' => '2637',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGPD',
                'rules' => 'A',
                'start' => '4250',
                'stop' => '4257',
                'allow_duplicate' => false,
            ],

            // Sumburgh
            [
                'unit' => 'EGPB',
                'rules' => 'A',
                'start' => '2621',
                'stop' => '2630',
                'allow_duplicate' => false,
            ],

            // Leeds
            [
                'unit' => 'EGNM',
                'rules' => 'A',
                'start' => '2650',
                'stop' => '2653',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGNM',
                'rules' => 'A',
                'start' => '2655',
                'stop' => '2676',
                'allow_duplicate' => false,
            ],

            // Swanwick Military
            [
                'unit' => 'EGVV',
                'rules' => 'A',
                'start' => '3301',
                'stop' => '3303',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGVV',
                'rules' => 'A',
                'start' => '3310',
                'stop' => '3367',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGVV',
                'rules' => 'A',
                'start' => '4610',
                'stop' => '4667',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGVV',
                'rules' => 'A',
                'start' => '6101',
                'stop' => '6157',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGVV',
                'rules' => 'A',
                'start' => '6401',
                'stop' => '6457',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGQS',
                'rules' => 'A',
                'start' => '3301',
                'stop' => '3303',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGQS',
                'rules' => 'A',
                'start' => '3310',
                'stop' => '3367',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGQS',
                'rules' => 'A',
                'start' => '4610',
                'stop' => '4667',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGQS',
                'rules' => 'A',
                'start' => '6101',
                'stop' => '6157',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGQS',
                'rules' => 'A',
                'start' => '6401',
                'stop' => '6457',
                'allow_duplicate' => false,
            ],

            // Jersey
            [
                'unit' => 'EGJJ',
                'rules' => 'A',
                'start' => '3601',
                'stop' => '3647',
                'allow_duplicate' => false,
            ],

            // Cardiff
            [
                'unit' => 'EGFF',
                'rules' => 'A',
                'start' => '3601',
                'stop' => '3657',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGFF',
                'rules' => 'A',
                'start' => '3646',
                'stop' => '3657',
                'allow_duplicate' => false,
            ],


            // Southampton / SOLENT
            [
                'unit' => 'EGHI',
                'rules' => 'A',
                'start' => '3660',
                'stop' => '3665',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'SOLENT',
                'rules' => 'A',
                'start' => '3660',
                'stop' => '3665',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGHI',
                'rules' => 'A',
                'start' => '3667',
                'stop' => '3677',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'SOLENT',
                'rules' => 'A',
                'start' => '3660',
                'stop' => '3677',
                'allow_duplicate' => false,
            ],

            // Norwich
            [
                'unit' => 'EGSH',
                'rules' => 'A',
                'start' => '3701',
                'stop' => '3710',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGSH',
                'rules' => 'A',
                'start' => '7351',
                'stop' => '7377',
                'allow_duplicate' => false,
            ],

            // Newcastle
            [
                'unit' => 'EGNT',
                'rules' => 'A',
                'start' => '3720',
                'stop' => '3727',
                'allow_duplicate' => false,
            ],

            // Brize
            [
                'unit' => 'EGVN',
                'rules' => 'A',
                'start' => '3701',
                'stop' => '3736',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGVN',
                'rules' => 'A',
                'start' => '3740',
                'stop' => '3745',
                'allow_duplicate' => false,
            ],

            // Guernsey
            [
                'unit' => 'EGJB',
                'rules' => 'A',
                'start' => '3701',
                'stop' => '3747',
                'allow_duplicate' => false,
            ],

            // Lossie
            [
                'unit' => 'EGQS',
                'rules' => 'A',
                'start' => '3701',
                'stop' => '3767',
                'allow_duplicate' => false,
            ],

            // Valley
            [
                'unit' => 'EGOV',
                'rules' => 'A',
                'start' => '3720',
                'stop' => '3727',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGOV',
                'rules' => 'A',
                'start' => '3730',
                'stop' => '3736',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGOV',
                'rules' => 'A',
                'start' => '3740',
                'stop' => '3747',
                'allow_duplicate' => false,
            ],

            // Gatwick
            [
                'unit' => 'EGKK',
                'rules' => 'A',
                'start' => '3750',
                'stop' => '3761',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGKK',
                'rules' => 'A',
                'start' => '3764',
                'stop' => '3766',
                'allow_duplicate' => false,
            ],

            // Shoreham
            [
                'unit' => 'EGKA',
                'rules' => 'I',
                'start' => '3762',
                'stop' => '3762',
                'allow_duplicate' => true,
            ],
            [
                'unit' => 'EGKA',
                'rules' => 'V',
                'start' => '3763',
                'stop' => '3763',
                'allow_duplicate' => true,
            ],

            // Belfast City
            [
                'unit' => 'EGAC',
                'rules' => 'A',
                'start' => '4250',
                'stop' => '4267',
                'allow_duplicate' => false,
            ],

            // Humberside
            [
                'unit' => 'EGNJ',
                'rules' => 'A',
                'start' => '4250',
                'stop' => '4277',
                'allow_duplicate' => false,
            ],

            // London
            [
                'unit' => 'LON',
                'rules' => 'A',
                'start' => '4307',
                'stop' => '4317',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'LON',
                'rules' => 'A',
                'start' => '4370',
                'stop' => '4377',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'LON',
                'rules' => 'A',
                'start' => '5001',
                'stop' => '5012',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'LTC',
                'rules' => 'A',
                'start' => '4307',
                'stop' => '4317',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'LTC',
                'rules' => 'A',
                'start' => '4370',
                'stop' => '4377',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'LTC',
                'rules' => 'A',
                'start' => '5001',
                'stop' => '5012',
                'allow_duplicate' => false,
            ],

            // Northolt
            [
                'unit' => 'EGWU',
                'rules' => 'A',
                'start' => '4360',
                'stop' => '4367',
                'allow_duplicate' => false,
            ],

            // Coventry
            [
                'unit' => 'EGBE',
                'rules' => 'A',
                'start' => '4362',
                'stop' => '4367',
                'allow_duplicate' => false,
            ],

            // Oxford
            [
                'unit' => 'EGTK',
                'rules' => 'A',
                'start' => '4501',
                'stop' => '4516',
                'allow_duplicate' => false,
            ],

            // Prestwick
            [
                'unit' => 'EGPK',
                'rules' => 'A',
                'start' => '4501',
                'stop' => '4517',
                'allow_duplicate' => false,
            ],

            // Isle of Mann
            [
                'unit' => 'EGNS',
                'rules' => 'A',
                'start' => '4550',
                'stop' => '4567',
                'allow_duplicate' => false,
            ],

            // East Midlands
            [
                'unit' => 'EGNX',
                'rules' => 'A',
                'start' => '4550',
                'stop' => '4570',
                'allow_duplicate' => false,
            ],

            // Standsted/Luton/Essex
            [
                'unit' => 'EGSS',
                'rules' => 'A',
                'start' => '4670',
                'stop' => '4676',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGGW',
                'rules' => 'A',
                'start' => '4670',
                'stop' => '4676',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'ESSEX',
                'rules' => 'A',
                'start' => '4670',
                'stop' => '4676',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGSS',
                'rules' => 'A',
                'start' => '7402',
                'stop' => '7414',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGGW',
                'rules' => 'A',
                'start' => '7402',
                'stop' => '7414',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'ESSEX',
                'rules' => 'A',
                'start' => '7402',
                'stop' => '7414',
                'allow_duplicate' => false,
            ],

            // Liverpool
            [
                'unit' => 'EGGP',
                'rules' => 'A',
                'start' => '5051',
                'stop' => '5067',
                'allow_duplicate' => false,
            ],

            // Southend
            [
                'unit' => 'EGMC',
                'rules' => 'A',
                'start' => '5051',
                'stop' => '5067',
                'allow_duplicate' => false,
            ],

            // Bristol
            [
                'unit' => 'EGGD',
                'rules' => 'A',
                'start' => '5050',
                'stop' => '5067',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGGD',
                'rules' => 'A',
                'start' => '5071',
                'stop' => '5076',
                'allow_duplicate' => false,
            ],

            // Inverness
            [
                'unit' => 'EGPE',
                'rules' => 'A',
                'start' => '6160',
                'stop' => '6176',
                'allow_duplicate' => false,
            ],

            // Cambridge
            [
                'unit' => 'EGSC',
                'rules' => 'A',
                'start' => '6160',
                'stop' => '6176',
                'allow_duplicate' => false,
            ],

            // Doncaster
            [
                'unit' => 'EGSC',
                'rules' => 'A',
                'start' => '6171',
                'stop' => '6177',
                'allow_duplicate' => false,
            ],

            // Aldergrove
            [
                'unit' => 'EGAA',
                'rules' => 'A',
                'start' => '7030',
                'stop' => '7044',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGAA',
                'rules' => 'A',
                'start' => '7046',
                'stop' => '7047',
                'allow_duplicate' => false,
            ],

            // TC Thames / TC Heathrow
            [
                'unit' => 'THAMES',
                'rules' => 'A',
                'start' => '7030',
                'stop' => '7046',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLL',
                'rules' => 'A',
                'start' => '7030',
                'stop' => '7046',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'THAMES',
                'rules' => 'A',
                'start' => '7050',
                'stop' => '7056',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLL',
                'rules' => 'A',
                'start' => '7050',
                'stop' => '7056',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'THAMES',
                'rules' => 'A',
                'start' => '7070',
                'stop' => '7076',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGLL',
                'rules' => 'A',
                'start' => '7070',
                'stop' => '7076',
                'allow_duplicate' => false,
            ],

            // Durham
            [
                'unit' => 'EGNV',
                'rules' => 'A',
                'start' => '7030',
                'stop' => '7066',
                'allow_duplicate' => false,
            ],

            // Lydd
            [
                'unit' => 'EGMD',
                'rules' => 'V',
                'start' => '7066',
                'stop' => '7066',
                'allow_duplicate' => true,
            ],

            // Manchester
            [
                'unit' => 'EGCC',
                'rules' => 'A',
                'start' => '7350',
                'stop' => '7364',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGCC',
                'rules' => 'A',
                'start' => '7367',
                'stop' => '7373',
                'allow_duplicate' => false,
            ],

            // Barton
            [
                'unit' => 'EGCB',
                'rules' => 'A',
                'start' => '7365',
                'stop' => '7365',
                'allow_duplicate' => true,
            ],

            // Scottish
            [
                'unit' => 'SCO',
                'rules' => 'A',
                'start' => '0004',
                'stop' => '0005',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'SCO',
                'rules' => 'A',
                'start' => '0025',
                'stop' => '0025',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'SCO',
                'rules' => 'A',
                'start' => '3601',
                'stop' => '3632',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'STC',
                'rules' => 'A',
                'start' => '0004',
                'stop' => '0005',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'STC',
                'rules' => 'A',
                'start' => '0025',
                'stop' => '0025',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'STC',
                'rules' => 'A',
                'start' => '3601',
                'stop' => '3632',
                'allow_duplicate' => false,
            ],
        ];

        $processedOwners = [];
        // Process the ranges
        foreach ($rangeInfo as $range) {
            // Create the range owner and unit if we haven't already
            if (!isset($processedOwners[$range['unit']])) {
                // Create the range owner
                $rangeOwner = new SquawkRangeOwner();
                $rangeOwner->save();

                // Create the unit range
                $unit = new SquawkUnit();
                $unit->unit = $range['unit'];
                $unit->squawk_range_owner_id = $rangeOwner->id;
                $unit->save();
                $processedOwners[$range['unit']] = $unit;
            }

            // Create the range
            $squawkRange = new Range();
            $squawkRange->start = $range['start'];
            $squawkRange->stop = $range['stop'];
            $squawkRange->rules = $range['rules'];
            $squawkRange->allow_duplicate = $range['allow_duplicate'];
            $squawkRange->squawkRangeOwner()->associate($processedOwners[$range['unit']]);
            $squawkRange->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $allUnit = SquawkUnit::all();
        foreach ($allUnit as $unit) {
            $unit->delete();
            SquawkRangeOwner::where('id', $unit->squawk_range_owner_id)->delete();
        }
    }
}
