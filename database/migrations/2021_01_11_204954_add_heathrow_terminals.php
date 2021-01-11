<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddHeathrowTerminals extends Migration
{
    /**
     * Run the migrations.cre
     *
     * @return void
     */
    public function up()
    {
        // Get the heathrow ID
        $heathrow = DB::table('airfield')
            ->where('code', 'EGLL')
            ->first()
            ->id;

        // Assign Swiss to Stand 218 for instances where someone takes an A330 to Heathrow
        $stand218 = DB::table('stands')
            ->where('airfield_id', $heathrow)
            ->where('identifier', '218')
            ->first()
            ->id;

        $swiss = DB::table('airlines')
            ->where('icao_code', 'SWR')
            ->first()
            ->id;

        DB::table('airline_stand')->insert(
            [
                'stand_id' => $stand218,
                'airline_id' => $swiss,
                'created_at' => Carbon::now(),
            ]
        );

        // Create Heathrow terminals
        $terminal2a = $this->createTerminal($heathrow, 'EGLL_T2A', 'Heathrow Terminal 2A');
        $terminal2b = $this->createTerminal($heathrow, 'EGLL_T2B', 'Heathrow Terminal 2B');
        $terminal3 = $this->createTerminal($heathrow, 'EGLL_T3', 'Heathrow Terminal 3');
        $terminal4 = $this->createTerminal($heathrow, 'EGLL_T4', 'Heathrow Terminal 4');
        $terminal5a = $this->createTerminal($heathrow, 'EGLL_T5A', 'Heathrow Terminal 5A');
        $terminal5b = $this->createTerminal($heathrow, 'EGLL_T5B', 'Heathrow Terminal 5B');
        $terminal5c = $this->createTerminal($heathrow, 'EGLL_T5C', 'Heathrow Terminal 5C');

        // Assign stands to a given terminal
        $this->assignStandsToTerminal($heathrow, $terminal2a, '216');
        $this->assignStandsToTerminal($heathrow, $terminal2a, '217');
        $this->assignStandsToTerminal($heathrow, $terminal2a, '218%');
        $this->assignStandsToTerminal($heathrow, $terminal2a, '219');
        $this->assignStandsToTerminal($heathrow, $terminal2a, '22%');
        $this->assignStandsToTerminal($heathrow, $terminal2b, '23%');
        $this->assignStandsToTerminal($heathrow, $terminal2b, '24%');
        $this->assignStandsToTerminal($heathrow, $terminal3, '3%');
        $this->assignStandsToTerminal($heathrow, $terminal4, '40%');
        $this->assignStandsToTerminal($heathrow, $terminal4, '41%');
        $this->assignStandsToTerminal($heathrow, $terminal4, '42%');
        $this->assignStandsToTerminal($heathrow, $terminal5a, '50%');
        $this->assignStandsToTerminal($heathrow, $terminal5a, '51%');
        $this->assignStandsToTerminal($heathrow, $terminal5a, '52%');
        $this->assignStandsToTerminal($heathrow, $terminal5b, '53%');
        $this->assignStandsToTerminal($heathrow, $terminal5b, '54%');
        $this->assignStandsToTerminal($heathrow, $terminal5c, '55%');
        $this->assignStandsToTerminal($heathrow, $terminal5c, '56%');

        // Assign airlines to their terminals
        $this->assignAirlinesToTerminal($terminal2a);
        $this->assignAirlinesToTerminal($terminal2b);
        $this->assignAirlinesToTerminal($terminal3);
        $this->assignAirlinesToTerminal($terminal4);
        $this->assignAirlinesToTerminal($terminal5a);
        $this->assignAirlinesToTerminal($terminal5b);
        $this->assignAirlinesToTerminal($terminal5c);
    }

    private function createTerminal(int $airfieldId, string $key, string $description): int
    {
        return (int) DB::table('terminals')
            ->insertGetId(
                [
                    'airfield_id' => $airfieldId,
                    'key' => $key,
                    'description' => $description,
                    'created_at' => Carbon::now(),
                ]
            );
    }

    private function assignStandsToTerminal(int $airfieldId, int $terminalId, string $standIdentifierPattern): void
    {
        DB::table('stands')
            ->where('airfield_id', $airfieldId)
            ->where('identifier', 'like', $standIdentifierPattern)
            ->update(['terminal_id' => $terminalId, 'updated_at' => Carbon::now()]);
    }

    private function assignAirlinesToTerminal(int $terminalId): void
    {
        // Don't add terminals for BA.
        $airlinesAtTerminal = DB::table('airline_stand')
            ->join('stands', 'airline_stand.stand_id', '=', 'stands.id')
            ->join('airlines', 'airline_stand.airline_id', '=', 'airlines.id')
            ->where('stands.terminal_id', $terminalId)
            ->whereNotIn('airlines.icao_code', ['BAW', 'SHT', 'IBE', 'IBS'])
            ->select('airline_stand.airline_id')
            ->distinct()
            ->get()
            ->toArray();

        $formattedAirlines = [];
        foreach ($airlinesAtTerminal as $airline) {
            $formattedAirlines[] = [
                'airline_id' => $airline->airline_id,
                'terminal_id' => $terminalId,
                'created_at' => Carbon::now(),
            ];
        }

        DB::table('airline_terminal')
            ->insert($formattedAirlines);
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
}
