<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddIsCargoToAirlinesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airlines', function (Blueprint $table) {
            $table->boolean('is_cargo')
                ->after('callsign')
                ->default(false)
                ->comment('Returns whether the airline is a cargo airline');
        });

        DB::table('airlines')
            ->where('name', 'LIKE', '%cargo%')
            ->orWhere('callsign', 'LIKE', '%cargo%')
            ->orWhereIn('icao_code', ['FDX', 'UPS', 'DHX', 'DAE', 'DHK', 'LDL'])
            ->update(['is_cargo' => true]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airlines', function (Blueprint $table) {
            $table->dropColumn('is_cargo');
        });
    }
}
