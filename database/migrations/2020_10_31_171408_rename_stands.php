<?php

use App\Models\Airfield\Airfield;
use App\Models\Stand\Stand;
use App\Services\DependencyService;
use App\Services\StandService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Migrations\Migration;

class RenameStands extends Migration
{
    const STANDS_TO_RENAME_FILE = __DIR__ . '/../data/stands/2020/torename.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $stands = fopen(self::STANDS_TO_RENAME_FILE, 'r');
        while ($line = fgetcsv($stands)) {
            Stand::whereHas('airfield', function (Builder $airfield) use ($line) {
                $airfield->where('code', $line[0]);
            })
                ->where('identifier', $line[1])
                ->update(['identifier' => $line[2]]);
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
