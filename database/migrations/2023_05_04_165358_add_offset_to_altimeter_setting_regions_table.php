<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('altimeter_setting_region', function (Blueprint $table) {
            $table->tinyInteger('adjustment')
                ->after('name')
                ->default(-1)
                ->comment('The lowest QNH in the region is adjusted by this amount to produce the RPS');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('altimeter_setting_region', function (Blueprint $table) {
            $table->dropColumn('adjustment');
        });
    }
};
