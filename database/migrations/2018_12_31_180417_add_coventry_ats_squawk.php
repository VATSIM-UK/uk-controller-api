<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddCoventryAtsSquawk extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('squawk_range')->where('squawk_range_owner_id', 30)->delete();
        DB::table('squawk_range')->insert(
            [
                'squawk_range_owner_id' => 30,
                'start' => '0420',
                'stop' => '0420',
                'rules' => 'A',
                'allow_duplicate' => true,
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
        DB::table('squawk_range')->where('squawk_range_owner_id', 30)->delete();
    }
}
