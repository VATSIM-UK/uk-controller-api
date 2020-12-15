<?php

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use Illuminate\Database\Migrations\Migration;

class AddStandPairs extends Migration
{
    const STAND_PAIRS_FILE = __DIR__ . '/../data/stands/2020/standpairs.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pairs = fopen(self::STAND_PAIRS_FILE, 'r');
        while ($line = fgetcsv($pairs)) {
            $airfieldId = Airfield::where('code', $line[0])->first()->id;
            $firstStand = Stand::where('airfield_id', $airfieldId)
                    ->where('identifier', $line[1])
                    ->first();
            $secondStand = Stand::where('airfield_id', $airfieldId)
                    ->where('identifier', $line[2])
                    ->first();

            if (!$firstStand || !$secondStand) {
                dd($firstStand, $secondStand, $line);
            }

            $firstStand->pairedStands()->attach($secondStand);
            $secondStand->pairedStands()->attach($firstStand);
        }
        fclose($pairs);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // There is no return - if this fails we'll probably be dropping table anyway.
    }
}
