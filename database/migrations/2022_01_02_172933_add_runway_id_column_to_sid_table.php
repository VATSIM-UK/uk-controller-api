<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRunwayIdColumnToSidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->unsignedBigInteger('runway_id')
                ->after('airfield_id')
                ->comment('The runway that this SID is used on');

            // We're now allowing identifier not to be unique for an airfield
            $table->dropIndex('sid_identifier_airfield_id_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sid', function (Blueprint $table) {
            $table->unique(['airfield_id', 'identifier'], 'sid_identifier_airfield_id_unique');
            $table->dropColumn('runway_id');
        });
    }
}
