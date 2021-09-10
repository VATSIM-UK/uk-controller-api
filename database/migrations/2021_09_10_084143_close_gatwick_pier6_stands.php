<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CloseGatwickPier6Stands extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('stands')
            ->where('airfield_id', DB::table('airfield')->where('code', 'EGKK')->first()->id)
            ->whereIn('identifier', ['109', '110', '110L', '110R', '111'])
            ->update(['closed_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
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
