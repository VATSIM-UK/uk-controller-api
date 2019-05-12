<?php

use App\Models\Hold\HoldRestriction;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;

class AddHoldRestrictions extends Migration
{
    const MSL = 'MSL';
    const MSLP1 = 'MSL+1';
    const MSLP2 = 'MSL+2';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $restrictions = [
            // WILLO
            [
                'hold_id' => 1,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGKK'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // TIMBA
            [
                'hold_id' => 2,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGKK'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // DAYNE
            [
                'hold_id' => 3,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGCC', '05L', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 3,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGCC', '05R', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 3,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGCC', '23L', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 3,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGCC', '23R', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // ROSUN
            [
                'hold_id' => 4,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGCC', '05L', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 4,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGCC', '05R', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 4,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGCC', '23L', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 4,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGCC', '23R', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // MIRSI
            [
                'hold_id' => 5,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGCC', '05L', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 5,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGCC', '05R', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 5,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGCC', '23L', 'any'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 5,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGCC', '23R', 'any'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],

            // BIG
            [
                'hold_id' => 6,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGLL'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // OCK
            [
                'hold_id' => 7,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGLL'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // BNN
            [
                'hold_id' => 8,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGLL'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // LAM
            [
                'hold_id' => 9,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGLL'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => 9,
                'restriction' => $this->createLevelBlockRestriction([13000]),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // LOREL
            [
                'hold_id' => 10,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGSS'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // ABBOT
            [
                'hold_id' => 11,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGSS', null, null, 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // BRI
            [
                'hold_id' => 12,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGGD'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // CDF
            [
                'hold_id' => 13,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGFF'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // TIPOD
            [
                'hold_id' => 14,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGGP'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // KEGUN
            [
                'hold_id' => 15,
                'restriction' => $this->createMinimumRestriction(self::MSLP1, 'EGGP'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // TWEED
            [
                'hold_id' => 20,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGPH'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // STIRA
            [
                'hold_id' => 21,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGPH'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // LANAK
            [
                'hold_id' => 22,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGPF'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // GOW
            [
                'hold_id' => 23,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGPF'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // GROVE
            [
                'hold_id' => 24,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGBB'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // CHASE
            [
                'hold_id' => 25,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGBB'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // ROKUP
            [
                'hold_id' => 26,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGNX'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // PIGOT
            [
                'hold_id' => 27,
                'restriction' => $this->createMinimumRestriction(self::MSL, 'EGBB', null, null, 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
        ];

        HoldRestriction::insert($restrictions);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        HoldRestriction::truncate();
    }

    /**
     * Create a level block restriction
     *
     * @param array $levels
     * @return false|string
     */
    private function createLevelBlockRestriction(array $levels)
    {
        $data = [];
        $data['type'] = 'level-block';
        $data['levels'] = $levels;

        return json_encode($data);
    }


    /**
     * Create a minimum level restriction
     *
     * @param string $level The level restriction
     * @param string $mslTarget The airfield to use the MSL at if not
     * @param null|string $activeRunway The active runway designation
     * @param null|string $runwayType The type of runway, either arrival, departure or any
     * @param null|int $override The overriding level
     * @return false|string
     */
    private function createMinimumRestriction(
        string $level,
        string $mslTarget,
        string $activeRunway = null,
        string $runwayType = null,
        int $override = null
    ) {
        $data = [];
        $data['type'] = 'minimum-level';
        $data['level'] = $level;
        $data['target'] = $mslTarget;

        if ($activeRunway) {
            $data['runway']['designator'] = $activeRunway;
        }

        if ($runwayType) {
            $data['runway']['type'] = $runwayType;
        }

        if ($override) {
            $data['override'] = $override;
        }

        return json_encode($data);
    }
}
