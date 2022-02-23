<?php

use App\Models\Controller\ControllerPosition;
use Illuminate\Database\Migrations\Migration;

class ControllerFrequencyConversions extends Migration
{
    const TO_UPDATE_PATTERN = '/^(\d{3})\.(\d)([27])$/';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $toUpdate = [];
        foreach (ControllerPosition::all() as $position) {
            $matches = [];
            if (preg_match(self::TO_UPDATE_PATTERN, (string)$position->frequency, $matches) !== 1) {
                continue;
            }

            $toUpdate[] = [
                'callsign' => $position->callsign,
                'frequency' => sprintf('%s.%s%s5', $matches[1], $matches[2], $matches[3]),
            ];
        }

        ControllerPosition::upsert(
            $toUpdate,
            ['callsign'],
            ['frequency']
        );
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
