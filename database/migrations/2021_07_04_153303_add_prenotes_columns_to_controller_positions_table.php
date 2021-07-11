<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddPrenotesColumnsToControllerPositionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('controller_positions', function (Blueprint $table) {
            $table->boolean('sends_prenotes')
                ->after('receives_departure_releases')
                ->default(false)
                ->comment('Whether the controller position can send prenotes');

            $table->boolean('receives_prenotes')
                ->after('sends_prenotes')
                ->default(false)
                ->comment('Whether the controller position can receive prenotes');
        });

        DB::table('controller_positions')
            ->update(
                [
                    'sends_prenotes' => DB::raw('`requests_departure_releases`'),
                    'receives_prenotes' => DB::raw('`receives_departure_releases`'),
                    'updated_at' => \Carbon\Carbon::now(),
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
        Schema::table('controller_positions', function (Blueprint $table) {
            //
        });
    }
}
