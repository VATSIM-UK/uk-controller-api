<?php

use App\Models\Aircraft\WakeCategory;
use App\Models\Airfield\Airfield;
use App\Models\Airfield\Terminal;
use App\Models\Stand\Stand;
use App\Models\Stand\StandType;
use App\Services\SectorfileService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class EastMidlandsStands extends Migration
{
    const STANDS_TO_ADD = [
        '81' => [
            '524940.68N 0011900.05W',
            'LM',
            ['80', '82'],
        ],
        '82' => [
            '524940.68N 0011900.05W',
            'LM',
            ['81'],
        ],
        '83' => [
            '524940.68N 0011900.05W',
            'H',
        ],
        '84' => [
            '524940.68N 0011900.05W',
            'LM',
            [],
        ],
        '85' => [
            '524940.68N 0011900.05W',
            'UM',
            ['86'],
        ],
        '86' => [
            '524940.68N 0011900.05W',
            'LM',
            ['85'],
        ],
    ];

    const OTHER_PAIRS = [
        '80' => [
            '81',
        ],
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Remove stand 25
        Stand::where('identifier', '25')
            ->airfield('EGNX')
            ->delete();

        // Add stands 81-86
        $eastMidlands = Airfield::where('code', 'EGNX')->firstOrFail()->id;
        $eastApron = Terminal::where('key', 'EGNX_E')->firstOrFail()->id;
        $cargo = StandType::where('key', 'CARGO')->firstOrFail()->id;


        foreach (self::STANDS_TO_ADD as $identifier => $details) {
            $coordinateSplit = explode(' ', $details[0]);
            $coordinate = SectorfileService::coordinateFromNats($coordinateSplit[0], $coordinateSplit[1]);

            Stand::create(
                [
                    'identifier' => $identifier,
                    'airfield_id' => $eastMidlands,
                    'latitude' => $coordinate->getLat(),
                    'longitude' => $coordinate->getLng(),
                    'terminal_id' => $eastApron,
                    'type_id' => $cargo,
                    'wake_category_id' => WakeCategory::whereHas('scheme', function (Builder $scheme) {
                        $scheme->uk();
                    })->where('code', $details[1])->firstOrFail()->id,
                    'assignment_priority' => 2,
                ]
            );
        }

        foreach (self::STANDS_TO_ADD as $identifier => $details) {
            Stand::where('identifier', $identifier)
                ->airfield('EGNX')
                ->firstOrFail()
                ->pairedStands()
                ->attach(Stand::whereIn('identifier', $details[2])->airfield('EGNX')->pluck('id'));
        }

        foreach (self::OTHER_PAIRS as $identifier => $stands) {
            Stand::where('identifier', $identifier)
                ->airfield('EGNX')
                ->firstOrFail()
                ->pairedStands()
                ->attach(Stand::whereIn('identifier', $stands)->airfield('EGNX')->pluck('id')->toArray());
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
