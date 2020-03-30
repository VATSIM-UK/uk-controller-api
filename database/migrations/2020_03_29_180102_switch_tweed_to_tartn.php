<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SwitchTweedToTartn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('hold')
            ->where('fix', 'TWEED')
            ->update(
                [
                    'fix' => 'TARTN',
                    'inbound_heading' => 15,
                    'description' => 'TARTN',
                    'updated_at' => Carbon::now(),
                ]
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('hold')
            ->where('fix', 'TARTN')
            ->update(
                [
                    'fix' => 'TWEED',
                    'inbound_heading' => 196,
                    'description' => 'TWEED',
                    'updated_at' => Carbon::now(),
                ]
            );
    }
}
