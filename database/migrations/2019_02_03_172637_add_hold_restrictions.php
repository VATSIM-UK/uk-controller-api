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
                'hold_id' => '1',
                'restriction' => $this->createMinimumRestriction(self::MSL),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // TIMBA
            [
                'hold_id' => '2',
                'restriction' => $this->createMinimumRestriction(self::MSL),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // DAYNE
            [
                'hold_id' => '3',
                'restriction' => $this->createMinimumRestriction(self::MSL, '05L', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '3',
                'restriction' => $this->createMinimumRestriction(self::MSL, '05R', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '3',
                'restriction' => $this->createMinimumRestriction(self::MSL, '23L', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '3',
                'restriction' => $this->createMinimumRestriction(self::MSL, '23R', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // ROSUN
            [
                'hold_id' => '4',
                'restriction' => $this->createMinimumRestriction(self::MSLP1, '05L', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '4',
                'restriction' => $this->createMinimumRestriction(self::MSLP1, '05R', 'any', 7000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '4',
                'restriction' => $this->createMinimumRestriction(self::MSLP1, '23L', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '4',
                'restriction' => $this->createMinimumRestriction(self::MSLP1, '23R', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // MIRSI
            [
                'hold_id' => '5',
                'restriction' => $this->createMinimumRestriction(self::MSLP1, '05L', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '5',
                'restriction' => $this->createMinimumRestriction(self::MSLP1, '05R', 'any', 8000),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '5',
                'restriction' => $this->createMinimumRestriction(self::MSL, '23L'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '5',
                'restriction' => $this->createMinimumRestriction(self::MSL, '23R'),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],

            // BIG
            [
                'hold_id' => '6',
                'restriction' => $this->createMinimumRestriction(self::MSL),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // OCK
            [
                'hold_id' => '7',
                'restriction' => $this->createMinimumRestriction(self::MSL),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // BNN
            [
                'hold_id' => '8',
                'restriction' => $this->createMinimumRestriction(self::MSLP1),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            // LAM
            [
                'hold_id' => '9',
                'restriction' => $this->createMinimumRestriction(self::MSLP1),
                'created_at' => Carbon::now()->toDateTimeString(),
            ],
            [
                'hold_id' => '9',
                'restriction' => $this->createLevelBlockRestriction([13000]),
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
     * @param null|string $activeRunway The active runway designation
     * @param null|string $runwayType The type of runway, either arrival, departure or any
     * @param null|int $override The overriding level
     * @return false|string
     */
    private function createMinimumRestriction($level, $activeRunway = null, $runwayType = null, $override = null)
    {
        $data = [];
        $data['type'] = 'minimum-level';
        $data['level'] = $level;

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
