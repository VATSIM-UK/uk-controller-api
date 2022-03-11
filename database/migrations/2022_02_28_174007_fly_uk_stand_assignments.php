<?php

use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class FlyUkStandAssignments extends Migration
{
    const TERMINALS = [
        'EGLL_T2A',
        'EGLL_T2B',
    ];

    const STANDS_MAINSTREAM = [
        'EGKK' => [
            '46',
            '47',
            '47L',
            '47R',
            '48',
            '48L',
            '48R',
            '49',
            '49L',
            '49R',
            '50',
            '51',
            '51L',
            '51R',
            '52',
            '52L',
            '52R',
            '53',
            '54',
            '101',
            '102',
            '103',
            '104',
            '105',
            '106',
            '107',
            '109',
            '110',
            '110L',
            '110R',
            '111',
            '112',
            '113',
        ],
        'EGCC' => [
            '50',
            '51',
            '52',
            '53',
        ],
        'EGPH' => [
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
        ],
        'EGLC' => [
            '9',
            '10',
            '11',
            '12',
        ],
        'EGBB' => [
            '40',
            '40L',
            '41C',
            '41L',
            '41R',
            '42C',
            '42L',
            '42R',
            '54C',
            '54L',
            '54R',
            '55C',
            '55L',
            '55R',
            '56C',
            '56L',
            '56R',
        ],
        'EGHI' => [
            '1',
            '2',
            '3',
            '4',
            '5',
        ],
        'EGSS' => [
            '20',
            '21',
            '22L',
            '22R',
            '22',
            '23',
            '23L',
            '23R',
        ],
        'EGPF' => [
            '27',
            '28',
            '29',
            '30',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
        ],
        'EGGD' => [
            '1',
            '2',
        ],
        'EGFF' => [
            '9',
            '10',
        ],
        'EGGW' => [
            '6',
            '7',
            '8',
            '9',
        ],
        'EGNM' => [
            '7',
            '8',
        ],
        'EGAA' => [
            '15',
            '16',
            '21',
            '22',
        ],
        'EGJJ' => [
            '9',
            '10',
        ],
    ];

    const STANDS_MAINSTREAM_DOMESTIC = [
        'EGCC' => [
            '4',
            '5',
            '6',
            '7',
            '8',
            '9',
            '10',
            '11',
            '22',
            '23',
            '24',
            '25',
            '26',
            '27',
        ],
        'EGPH' => [
            '19',
            '20',
            '21',
            '22',
            '23',
            '24',
        ],
        'EGPF' => [
            '14',
            '15',
            '16',
            '17',
            '18',
        ],
        'EGNM' => [
            '9',
        ],
    ];

    const STANDS_FLY2 = [
        'EGCC' => [
            '202',
            '204',
            '206',
            '207',
            '208',
            '209',
            '210',
            '211',
            '212',
            '213',
            '214',
            '215',
            '216',
            '217',
            '218',
        ],
        'EGBB' => [
            '13',
            '14',
            '15',
        ],
        'EGPF' => [
            '27',
            '28',
            '29',
            '30',
            '31',
            '32',
            '33',
            '34',
            '35',
            '36',
        ],
        'EGNM' => [
            '7',
            '8',
            '10',
            '11',
        ],
    ];

    const STANDS_HIGHLAND_CONNECT = [
        'EGPH' => [
            '101',
            '102',
            '103',
            '104',
        ],
        'EGPF' => [
            '1',
            '2',
            '12',
        ],
    ];

    const STANDS_CARGO = [
        'EGSS' => [
            '1',
            '1L',
            '1R',
            '2',
            '3',
            '4',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $airline = Airline::where('icao_code', 'UKV')->firstOrFail();
        $this->setTerminals($airline);
        $this->setMainstreamStands($airline);
        $this->setMainstreamDomesticStands($airline);
        $this->setFly2Stands($airline);
        $this->setHighlandConnectStands($airline);
        $this->setCargoStands($airline);
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

    private function setTerminals(Airline $airline): void
    {
        $airline->terminals()->sync(Terminal::whereIn('key', self::TERMINALS)->pluck('id'));
    }

    private function setMainstreamStands(Airline $airline): void
    {
        foreach (self::STANDS_MAINSTREAM as $airfield => $stands) {
            $this->insertStands(
                $airline,
                $airfield,
                $stands,
                fn (Stand $stand) => [
                    $stand->id => [
                        'airline_id' => $airline->id,
                    ]
                ]
            );
        }
    }

    private function setMainstreamDomesticStands(Airline $airline): void
    {
        foreach (self::STANDS_MAINSTREAM_DOMESTIC as $airfield => $stands) {
            $this->insertStands(
                $airline,
                $airfield,
                $stands,
                fn (Stand $stand) => [
                    $stand->id => [
                        'airline_id' => $airline->id,
                        'destination' => 'EG',
                    ]
                ]
            );
        }
    }

    private function setFly2Stands(Airline $airline): void
    {
        foreach (self::STANDS_FLY2 as $airfield => $stands) {
            $this->insertStands(
                $airline,
                $airfield,
                $stands,
                fn (Stand $stand) => [
                    $stand->id => [
                        'airline_id' => $airline->id,
                        'callsign_slug' => '2',
                    ]
                ]
            );
        }
    }

    private function setHighlandConnectStands(Airline $airline): void
    {
        foreach (self::STANDS_HIGHLAND_CONNECT as $airfield => $stands) {
            $this->insertStands(
                $airline,
                $airfield,
                $stands,
                fn (Stand $stand) => [
                    $stand->id => [
                        'airline_id' => $airline->id,
                        'callsign_slug' => '4',
                    ]
                ]
            );
        }
    }

    private function setCargoStands(Airline $airline): void
    {
        foreach (self::STANDS_CARGO as $airfield => $stands) {
            $this->insertStands(
                $airline,
                $airfield,
                $stands,
                fn (Stand $stand) => [
                    $stand->id => [
                        'airline_id' => $airline->id,
                        'callsign_slug' => '7',
                    ]
                ]
            );
        }
    }

    private function insertStands(Airline $airline, string $airfield, array $stands, callable $mapFunction): void
    {
        $standsToInsert = Stand::whereIn('identifier', $stands)
            ->airfield($airfield)
            ->get()
            ->mapWithKeys($mapFunction)
            ->toArray();
        $airline->stands()->attach($standsToInsert);
    }
}
