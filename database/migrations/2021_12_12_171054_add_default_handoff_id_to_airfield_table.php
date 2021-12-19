<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDefaultHandoffIdToAirfieldTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('airfield', function (Blueprint $table) {
            $table->unsignedBigInteger('handoff_id')
                ->after('wake_category_scheme_id')
                ->nullable()
                ->comment('The default handoff for aircraft departing this airfield');

            $table->foreign('handoff_id')->references('id')->on('handoffs')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('airfield', function (Blueprint $table) {
            $table->dropColumn('handoff_id');
        });
    }
}
