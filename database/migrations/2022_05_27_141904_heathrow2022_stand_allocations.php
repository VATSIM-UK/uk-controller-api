<?php

use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class Heathrow2022StandAllocations extends Migration
{
    const ALLOCATION_DATA_FILE = __DIR__ . '/../data/stands/heathrow-2022-refresh/stand-refresh.csv';

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Up-front load stands and airlines
        $airlines = Airline::query()->select(['id', 'icao_code'])
            ->get()
            ->mapWithKeys(fn (Airline $airline) => [$airline->icao_code => $airline->id])
            ->toArray();

        $heathrowStands = Stand::query()
            ->airfield('EGLL')
            ->select(['id', 'identifier'])
            ->get()
            ->mapWithKeys(fn (Stand $stand) => [$stand->identifier => $stand->id])
            ->toArray();

        $standsByAirline = [];

        // Load all the stand data from file, the first row is just header so throw it away
        $standData = fopen(self::ALLOCATION_DATA_FILE, 'r');
        fgetcsv($standData);
        while ($line = fgetcsv($standData)) {
            if (empty($line)) {
                continue;
            }

            $standsByAirline[$airlines[$line[0]]][] = [
                'airline_id' => $airlines[$line[0]],
                'stand_id' => $heathrowStands[$line[1]],
                'destination' => empty($line[2]) ? null : $line[2],
                'not_before' => empty($line[3]) ? null : $line[3],
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        }
        fclose($standData);

        DB::transaction(function () use ($standsByAirline, $heathrowStands) {
            // For each airline, unsync all their current stands and then add new.
            foreach ($standsByAirline as $airline => $stands) {
                DB::table('airline_stand')
                    ->where('airline_id', $airline)
                    ->whereIn('stand_id', $heathrowStands)
                    ->delete();

                DB::table('airline_stand')
                    ->insert($stands);
            }
        });
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
