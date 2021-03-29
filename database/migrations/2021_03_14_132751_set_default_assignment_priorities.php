<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SetDefaultAssignmentPriorities extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('stands')->where('general_use', 1)
            ->update(['assignment_priority' => 1, 'updated_at' => Carbon::now()]);

        DB::table('stands')->where('general_use', 0)
            ->update(['assignment_priority' => 100, 'updated_at' => Carbon::now()]);
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
