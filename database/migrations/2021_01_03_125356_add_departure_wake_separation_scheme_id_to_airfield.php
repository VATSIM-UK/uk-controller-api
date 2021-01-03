<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddDepartureWakeSeparationSchemeIdToAirfield extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('airfield', function (Blueprint $table) {
            $ukSchemeId = DB::table('departure_wake_separation_schemes')
                ->where('key', 'UK')
                ->first()
                ->id;

            $table->unsignedBigInteger('departure_wake_separation_scheme_id')
                ->after('msl_calculation')
                ->default($ukSchemeId)
                ->comment('Which scheme to use for departure separation');

            $table->foreign('departure_wake_separation_scheme_id', 'departure_wake_separation_scheme_id')
                ->references('id')
                ->on('departure_wake_separation_schemes');
        });

        $recatSchemeId = DB::table('departure_wake_separation_schemes')
            ->where('key', 'RECAT_EU')
            ->first()
            ->id;

        DB::table('airfield')
            ->where('code', 'EGLL')
            ->update(
                [
                    'departure_wake_separation_scheme_id' => $recatSchemeId,
                    'updated_at' => Carbon::now()
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
        Schema::table('airfield', function (Blueprint $table) {
            $table->dropForeign('departure_wake_separation_scheme_id');
            $table->dropColumn('departure_wake_separation_scheme_id');
        });
    }
}
