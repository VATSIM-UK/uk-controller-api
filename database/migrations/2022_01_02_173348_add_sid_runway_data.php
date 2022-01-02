<?php

use App\Models\Airfield\Airfield;
use App\Models\Runway\Runway;
use App\Models\Sid;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Str;

class AddSidRunwayData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $file = fopen(__DIR__ . '/../data/runway/2022-runway-sids.csv', 'r+');
        while ($line = fgetcsv($file)) {
            $airfieldId = Airfield::where('code', $line[0])->firstOrFail()->id;
            Sid::where('airfield_id', $airfieldId)
                ->where('identifier', Str::remove('#', $line[2]))
                ->firstOrFail()
                ->update(
                    [
                        'runway_id' => Runway::where('airfield_id', $airfieldId)->where(
                            'identifier',
                            $line[1]
                        )->firstOrFail()->id
                    ]
                );
        }
        fclose($file);
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
