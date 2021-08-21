<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPriorityToAirlineStandTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->unsignedInteger('priority')
                ->after('stand_id')
                ->default(100)
                ->comment('How preferred the stand is, the lower the number, the more preferred');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airline_stand', function (Blueprint $table) {
            $table->dropColumn('priority');
        });
    }
}
