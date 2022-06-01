<?php

use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class HeathrowTerminals2022 extends Migration
{
    const TERMINALS = [
        'EGLL_T2A' => [
            'AEE',
            'AMC',
            'ASL',
            'AUA',
            'BLA',
            'CCA',
            'CTN',
            'DLH',
            'EIN',
            'EWG',
            'ITY',
            'LOG',
            'LOT',
            'LZB',
            'MSR',
            'ROT',
            'SAS',
            'SEH',
            'SWR',
            'SZS',
            'TAP',
            'TAR',
            'THY',
            'WIF',
        ],
        'EGLL_T2B' => [
            'AAR',
            'ACA',
            'AFL',
            'AHY',
            'AIC',
            'ANA',
            'AVA',
            'BEE',
            'ELY',
            'ETH',
            'EVA',
            'GFA',
            'ICE',
            'JBU',
            'KAC',
            'MAS',
            'MAU',
            'MSR',
            'OMA',
            'SIA',
            'SVA',
            'THA',
            'THY',
            'UAL',
        ],
        'EGLL_T3' => [
            'AAL',
            'AFR',
            'ALK',
            'BBC',
            'CHH',
            'CPA',
            'DAL',
            'ETD',
            'FIN',
            'IRA',
            'JAL',
            'KAL',
            'KLM',
            'KQA',
            'PAL',
            'QFA',
            'RJA',
            'RWD',
            'TAM',
            'UAE',
            'VIR',
            'VTI',
        ],
        'EGLL_T4' => [
            'CES',
            'CSN',
            'UZB',
        ],
        'EGLL_T5A' => [
            'BAW',
            'SHT',
            'IBE',
        ],
        'EGLL_T5B' => [
            'AAL',
            'BAW',
            'IBE',
            'QTR',
        ],
        'EGLL_T5C' => [
            'BAW',
            'IBE',
            'QTR',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $terminals = DB::table('terminals')
            ->whereIn('key', array_keys(self::TERMINALS))
            ->pluck('id');

        // Remove any airlines from all Heathrow Terminals
        DB::table('airline_terminal')
            ->whereIn('airline_id', Airline::whereIn('icao_code', collect(self::TERMINALS)->flatten())->pluck('id'))
            ->whereIn('terminal_id', $terminals)
            ->delete();

        // Add the airlines to each of the terminals
        foreach (self::TERMINALS as $terminal => $airlines) {
            Terminal::where('key', $terminal)
                ->first()
                ->airlines()
                ->attach(Airline::whereIn('icao_code', $airlines)->pluck('id'));
        }
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
