<?php

use App\Models\Airfield;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NewHeathrowSids extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create the new SIDs
        $heathrow = Airfield::where('code', 'EGLL')->first()->id;
        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $heathrow,
                    'identifier' => 'MAXIT1F',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $heathrow,
                    'identifier' => 'MAXIT1G',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $heathrow,
                    'identifier' => 'MODMI1J',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $heathrow,
                    'identifier' => 'MODMI1K',
                    'initial_altitude' => 6000,
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        // Update GASGU/GOGSI
        DB::table('sid')
            ->where('identifier', 'LIKE', 'GASGU%')
            ->orWhere('identifier', 'LIKE', 'GOGSI%')
            ->update(['identifier' => DB::raw('REPLACE(`identifier`, "1", "2")')]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Delete new SIDs
        DB::table('sid')->whereIn('identifier', ['MAXIT1F', 'MAXIT1G', 'MODMI1J', 'MODMI1K'])->delete();

        // Update GASGU/GOGSI
        DB::table('sid')
            ->where('identifier', 'LIKE', 'GASGU%')
            ->orWhere('identifier', 'LIKE', 'GOGSI%')
            ->update(['identifier' => DB::raw('REPLACE(`identifier`, "2", "1")')]);
    }
}
