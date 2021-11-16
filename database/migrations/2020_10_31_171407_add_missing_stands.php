<?php

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Services\DependencyService;
use App\Services\StandService;
use Illuminate\Database\Migrations\Migration;

class AddMissingStands extends Migration
{
    const MISSING_STANDS_FILE = __DIR__ . '/../data/stands/2020/missingstands.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $stands = fopen(self::MISSING_STANDS_FILE, 'r');
        while ($line = fgetcsv($stands)) {
            Stand::create(
                [
                    'airfield_id' => Airfield::where('code', $line[0])->first()->id,
                    'identifier' => $line[1],
                    'latitude' => $line[2],
                    'longitude' => $line[3],
                ]
            );
        }
        fclose($stands);
        DependencyService::touchDependencyByKey(StandService::STAND_DEPENDENCY_KEY);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There is no return.
    }
}
