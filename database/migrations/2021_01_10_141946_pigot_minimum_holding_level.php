<?php

use App\Services\DependencyService;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PigotMinimumHoldingLevel extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $pigot = DB::table('navaids')
            ->where('identifier', 'PIGOT')
            ->first()
            ->id;

        DB::table('holds')
            ->where('navaid_id', $pigot)
            ->update(['minimum_level' => 8000, 'updated_at' => Carbon::now()]);

        DependencyService::touchDependencyByKey('DEPENDENCY_HOLDS');
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
