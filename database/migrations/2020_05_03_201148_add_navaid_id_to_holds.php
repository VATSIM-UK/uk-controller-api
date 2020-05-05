<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNavaidIdToHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->unsignedBigInteger('navaid_id')
                ->after('id')
                ->comment('The navaid around which the hold is based');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->dropColumn('navaid_id');
        });
    }
}
