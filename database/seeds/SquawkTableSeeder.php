<?php

use App\Models\Squawks\Range;
use App\Models\Squawks\SquawkGeneral;
use App\Models\Squawks\SquawkRangeOwner;
use App\Models\Squawks\SquawkUnit;
use Illuminate\Database\Seeder;

class SquawkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // General squawk seeds
        $generalSquawks = [
            [
                'departure_ident' => "EGKK",
                'arrival_ident' => "EGCC",
                'rules' => 'A',
                'start' => "1234",
                'stop' => "1234"
            ],
            [
                'departure_ident' => "EG",
                'arrival_ident' => "LF",
                'rules' => 'A',
                'start' => "2222",
                'stop' => "3333"
            ],
            [
                'departure_ident' => "EG",
                'arrival_ident' => null,
                'rules' => 'A',
                'start' => "3334",
                'stop' => "4444"
            ],
            [
                'departure_ident' => "EG",
                'arrival_ident' => "L",
                'rules' => 'A',
                'start' => "5555",
                'stop' => "6666"
            ],
            [
                'departure_ident' => "EGJJ",
                'arrival_ident' => "EGJB",
                'rules' => 'A',
                'start' => "8000",
                'stop' => "8002"
            ],
            [
                'departure_ident' => "KJFK",
                'arrival_ident' => "KJFK",
                'rules' => 'A',
                'start' => "7700",
                'stop' => "7700"
            ],
            [
                'departure_ident' => "CCAMS",
                'arrival_ident' => "CCAMS",
                'rules' => 'A',
                'start' => "0123",
                'stop' => "0200"
            ],
        ];

        foreach ($generalSquawks as $range) {
            // If we don't yet have range owner, create one. There should be one range owner per combination of arr/dep
            $rangeOwner = new SquawkRangeOwner();
            $rangeOwner->save();
            $processedOwners[$range['departure_ident'] . '|' . $range['arrival_ident']] = $rangeOwner;

            // Create the range information
            $general = new SquawkGeneral();
            $general->departure_ident = $range['departure_ident'];
            $general->arrival_ident = $range['arrival_ident'];
            $general->squawk_range_owner_id =
                $processedOwners[$range['departure_ident'] . '|' . $range['arrival_ident']]->id;
            $general->save();

            // Create the range
            $squawkRange = new Range();
            $squawkRange->start = $range['start'];
            $squawkRange->stop = $range['stop'];
            $squawkRange->rules = $range['rules'];
            $squawkRange->allow_duplicate = false;
            $squawkRange->squawk_range_owner_id =
                $processedOwners[$range['departure_ident'] . '|' . $range['arrival_ident']]->id;
            $squawkRange->save();
        }

        // Unit specific seeds
        $unitSquawks = [
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
            [
                'unit' => 'EGXY',
                'rules' => 'I',
                'start' => '5555',
                'stop' => '5555',
                'allow_duplicate' => true,
            ],
            [
                'unit' => 'EGXY',
                'rules' => 'A',
                'start' => '6666',
                'stop' => '6666',
                'allow_duplicate' => true,
            ],
            [
                'unit' => 'EGPX',
                'rules' => 'A',
                'start' => '4723',
                'stop' => '4750',
                'allow_duplicate' => false,
            ],
            [
                'unit' => 'EGNA',
                'rules' => 'A',
                'start' => '2321',
                'stop' => '2321',
                'allow_duplicate' => false,
            ],
        ];

        $processedUnits = [];
        foreach ($unitSquawks as $range) {
            // If we don't yet have range owner, create one. There should be one range owner per unit
            if (!isset($processedUnits[$range['unit']])) {
                $rangeOwner = new SquawkRangeOwner();
                $rangeOwner->save();
                $processedUnits[$range['unit']] = $rangeOwner;

                // Create the range information
                $unit = new SquawkUnit();
                $unit->unit = $range['unit'];
                $unit->squawk_range_owner_id =
                    $processedUnits[$range['unit']]->id;
                $unit->save();
            }

            // Create the range
            $squawkRange = new Range();
            $squawkRange->start = $range['start'];
            $squawkRange->stop = $range['stop'];
            $squawkRange->rules = $range['rules'];
            $squawkRange->allow_duplicate = $range['allow_duplicate'];
            $squawkRange->squawk_range_owner_id =
                $processedUnits[$range['unit']]->id;
            $squawkRange->save();
        }
    }
}
