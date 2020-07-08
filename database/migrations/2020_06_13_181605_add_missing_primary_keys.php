<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddMissingPrimaryKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hold_profile_hold', function (Blueprint $table) {
            if (count(DB::select(DB::raw('SHOW KEYS FROM hold_profile_hold WHERE Key_name = "PRIMARY"'))) == 0) {
                $table->primary(['hold_profile_id', 'hold_id']);
            }
        });

        Schema::table('srd_note_srd_route', function (Blueprint $table) {
            if (count(DB::select(DB::raw('SHOW KEYS FROM srd_note_srd_route WHERE Key_name = "PRIMARY"'))) == 0) {
                $table->primary(['srd_note_id', 'srd_route_id']);
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
    }
}
