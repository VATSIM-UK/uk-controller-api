<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateNewGatwickHeathrowDepartures extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $gatwick = DB::table('airfield')->where('code', 'EGKK')->first()->id;
        $handoff = DB::table('handoffs')->where('key', 'EGKK_SID_BIG')->first()->id;
        DB::table('sid')->insert(
            [
                [
                    'airfield_id' => $gatwick,
                    'identifier' => 'BIG26L',
                    'initial_altitude' => 4000,
                    'handoff_id' => $handoff,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $gatwick,
                    'identifier' => 'BIG26R',
                    'initial_altitude' => 4000,
                    'handoff_id' => $handoff,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $gatwick,
                    'identifier' => 'BIG08R',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoff,
                    'created_at' => Carbon::now(),
                ],
                [
                    'airfield_id' => $gatwick,
                    'identifier' => 'BIG08L',
                    'initial_altitude' => 6000,
                    'handoff_id' => $handoff,
                    'created_at' => Carbon::now(),
                ],
            ]
        );

        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('sid')->whereIn('identifier', ['BIG26L', 'BIG26R', 'BIG08L', 'BIG08R']);
        DependencyService::touchDependencyByKey('DEPENDENCY_INITIAL_ALTITUDES');
        DependencyService::touchDependencyByKey('DEPENDENCY_SID_HANDOFF');
    }
}
