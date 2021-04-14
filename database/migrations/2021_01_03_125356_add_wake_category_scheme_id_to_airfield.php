<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddWakeCategorySchemeIdToAirfield extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airfield', function (Blueprint $table) {
            $ukSchemeId = DB::table('wake_category_schemes')
                ->where('key', 'UK')
                ->first()
                ->id;

            $table->unsignedBigInteger('wake_category_scheme_id')
                ->after('msl_calculation')
                ->default($ukSchemeId)
                ->comment('Which scheme to use for wake separation');

            $table->foreign('wake_category_scheme_id', 'wake_category_scheme_id')
                ->references('id')
                ->on('wake_category_schemes');
        });

        $recatSchemeId = DB::table('wake_category_schemes')
            ->where('key', 'RECAT_EU')
            ->first()
            ->id;

        DB::table('airfield')
            ->where('code', 'EGLL')
            ->update(
                [
                    'wake_category_scheme_id' => $recatSchemeId,
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
