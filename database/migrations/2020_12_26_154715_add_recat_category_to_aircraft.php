<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRecatCategoryToAircraft extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->unsignedBigInteger('recat_category_id')
                ->nullable()
                ->after('wake_turbulence_id')
                ->comment('The aircrafts RECAT category');

            $table->foreign('recat_category_id')->references('id')->on('recat_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropForeign('aircraft_recat_category_id_foreign');
            $table->dropColumn('recat_category_id');
        });
    }
}
