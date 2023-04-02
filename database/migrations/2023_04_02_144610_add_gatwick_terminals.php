<?php

use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

if (!class_exists(GatwickPiers::class)) {
    class GatwickPiers
    {
        public readonly int $gatwickAirfieldId;
        public readonly Terminal $pier1;
        public readonly Terminal $pier2South;
        public readonly Terminal $pier2North;
        public readonly Terminal $pier3;
        public readonly Terminal $pier4;
        public readonly Terminal $pier5;
        public readonly Terminal $pier6;
        public readonly Terminal $remote130s;
        public readonly Terminal $remote140s;

        public function __construct(int $gatwick)
        {
            $this->gatwickAirfieldId = $gatwick;
            $this->pier1 = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 1',
                ]
            );
            $this->pier2South = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 2 (South)',
                ]
            );
            $this->pier2North = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 2 (North)',
                ]
            );
            $this->pier3 = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 3',
                ]
            );
            $this->pier4 = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 4',
                ]
            );
            $this->pier5 = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 5',
                ]
            );
            $this->pier6 = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Pier 6',
                ]
            );
            $this->remote130s = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Remote 130\'s',
                ]
            );
            $this->remote140s = Terminal::create(
                [
                    'airfield_id' => $gatwick,
                    'description' => 'Remote 140\'s',
                ]
            );
        }
    }
}

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $piers = new GatwickPiers(Airfield::where('code', 'EGKK')->firstOrFail()->id);
        $this->allocateStandsToPiers($piers);
        $this->allocateAirlinesToPiers($piers);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // There is no down.
    }

    private function allocateStandsToPiers(GatwickPiers $piers): void
    {
        $this->allocateStandsToPier1($piers);
        $this->allocateStandsToPier2South($piers);
        $this->allocateStandsToPier2North($piers);
        $this->allocateStandsToPier3($piers);
        $this->allocateStandsToPier4($piers);
        $this->allocateStandsToPier5($piers);
        $this->allocateStandsToPier6($piers);
        $this->allocateStandsToRemote130s($piers);
        $this->allocateStandsToRemote140s($piers);
    }

    private function allocateStandsToPier1(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier1,
            $piers->gatwickAirfieldId,
            [
                '1',
                '2',
                '3',
                '4',
                '5',
            ]
        );
    }

    private function allocateStandsToPier2South(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier2South,
            $piers->gatwickAirfieldId,
            [
                '10',
                '11',
                '12',
                '14',
                '16',
                '18',
                '20',
                '22',
                '24',
                '28',
                '27',
            ]
        );
    }

    private function allocateStandsToPier2North(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier2North,
            $piers->gatwickAirfieldId,
            [
                '25',
                '23',
                '21',
                '19',
                '17',
                '15',
                '13L',
                '13R',
                '13',
            ]
        );
    }

    private function allocateStandsToPier3(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier3,
            $piers->gatwickAirfieldId,
            [
                '31',
                '31L',
                '31R',
                '32',
                '32L',
                '32R',
                '33',
                '33L',
                '33R',
                '34',
                '34L',
                '34R',
                '35',
                '35L',
                '35R',
                '36',
                '36L',
                '36R',
                '37',
                '38',
            ]
        );
    }

    private function allocateStandsToPier4(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier4,
            $piers->gatwickAirfieldId,
            [
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
                '54L',
                '54R',
            ]
        );
    }

    private function allocateStandsToPier5(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier5,
            $piers->gatwickAirfieldId,
            [
                '551',
                '552',
                '553',
                '554',
                '555',
                '557',
                '558',
                '559',
                '560',
                '561',
                '562',
                '563',
                '564',
                '564',
                '565',
                '566',
                '567',
                '568',
                '569',
                '570',
                '571',
                '572',
                '573',
                '574',
            ]
        );
    }

    private function allocateStandsToPier6(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->pier6,
            $piers->gatwickAirfieldId,
            [
                '112',
                '113',
                '101',
                '102',
                '107',
                '106',
                '105',
                '104',
                '103',
            ]
        );
    }

    private function allocateStandsToRemote130s(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->remote130s,
            $piers->gatwickAirfieldId,
            [
                '130',
                '131',
                '132',
                '133',
                '134',
                '135',
                '136',
            ]
        );
    }

    private function allocateStandsToRemote140s(GatwickPiers $piers): void
    {
        $this->allocateStandsToGivenPier(
            $piers->remote140s,
            $piers->gatwickAirfieldId,
            [
                '140',
                '141',
                '141L',
                '141R',
                '142',
                '142L',
                '142R',
                '143',
                '143L',
                '143R',
                '144',
                '144L',
                '144R',
                '145',
            ]
        );
    }

    private function allocateStandsToGivenPier(Terminal $pier, int $gatwickAirfieldId, array $stands): void
    {
        Stand::where('airfield_id', $gatwickAirfieldId)
            ->whereIn('identifier', $stands)
            ->update(['terminal_id' => $pier->id]);
    }

    private function allocateAirlinesToPiers(GatwickPiers $piers): void
    {
        $this->allocateAirlinesToPier1($piers);
        $this->allocateAirlinesToPier2South($piers);
        $this->allocateAirlinesToPier2North($piers);
        $this->allocateAirlinesToPier3($piers);
        $this->allocateAirlinesToPier4($piers);
        $this->allocateAirlinesToPier5($piers);
        $this->allocateAirlinesToPier6($piers);
        $this->allocateAirlinesToRemote130s($piers);
    }

    private function allocateAirlinesToPier1(GatwickPiers $piers): void
    {
        $pierId = $piers->pier1->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'AUR')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'BAW')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => 'EG',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'BAW')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => 'EI',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SHT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => 'EG',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SHT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => 'EI',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'CTN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EIN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => '64',
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => '65',
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NOZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NSZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                    'destination' => null,
                ],
            ]
        );
    }

    private function allocateAirlinesToPier2South(GatwickPiers $piers): void
    {
        $pierId = $piers->pier2South->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'AEA')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'AMC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'BAW')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SHT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'BTI')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'CAI')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'CTN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EIN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'ENT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => '64',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => '65',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'IBS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'LBT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'MAC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NOZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NSZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SXS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TAP')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TAR')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'THY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TOM')->first()?->id,
                    'terminal_id' => $pierId,
                    'callsign_slug' => '5',
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TOM')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'VUE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WZZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WUK')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
            ]
        );
    }

    private function allocateAirlinesToPier2North(GatwickPiers $piers): void
    {
        $pierId = $piers->pier2North->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'AEA')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'AIC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'AMC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'BAW')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SHT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'CTN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EIN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'ENT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => '64',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => '65',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'IBS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NBT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NOZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NSZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SXS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TAP')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'THY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TOM')->first()?->id,
                    'terminal_id' => $pierId,
                    'callsign_slug' => '5',
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TOM')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'VUE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WZZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WUK')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
            ]
        );
    }

    private function allocateAirlinesToPier3(GatwickPiers $piers): void
    {
        $pierId = $piers->pier3->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'AEA')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'AIC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'AMC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 3,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'BAW')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'SHT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'CTN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EIN')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'ENT')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                    'callsign_slug' => '64',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                    'callsign_slug' => '65',
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NOZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'NSZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WZZ')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WUK')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                    'callsign_slug' => null,
                ],
            ]
        );
    }

    private function allocateAirlinesToPier4(GatwickPiers $piers): void
    {
        $pierId = $piers->pier4->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EJU')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'FHY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'ICE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'JBU')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'RAM')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TSC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'WJA')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
            ]
        );
    }

    private function allocateAirlinesToPier5(GatwickPiers $piers): void
    {
        $pierId = $piers->pier5->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EJU')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'ICE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'JBU')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'QTR')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TSC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'UAE')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
            ]
        );
    }

    private function allocateAirlinesToPier6(GatwickPiers $piers): void
    {
        $pierId = $piers->pier6->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'EZY')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EZS')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'EJU')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'JBU')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
                [
                    'airline_id' => Airline::where('icao_code', 'TSC')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 1,
                ],
            ]
        );
    }

    private function allocateAirlinesToRemote130s(GatwickPiers $piers): void
    {
        $pierId = $piers->remote130s->id;
        $this->allocateAirlinesToGivenTerminal(
            [
                [
                    'airline_id' => Airline::where('icao_code', 'AUR')->first()?->id,
                    'terminal_id' => $pierId,
                    'priority' => 2,
                ],
            ]
        );
    }

    /**
     * Filter the data because, locally, some airlines don't exist.
     */
    private function allocateAirlinesToGivenTerminal(array $data): void
    {
        DB::table('airline_terminal')
            ->insert(
                array_filter($data, fn (array $pairing) => $pairing['airline_id'] !== null)
            );
    }
};
