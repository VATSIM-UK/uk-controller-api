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
        Schema::table('controller_positions', function (Blueprint $table) {
            $table->string('description')
                ->nullable()
                ->after('callsign');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('controller_positions', function (Blueprint $table) {
            $table->dropColumn('description');
        });
    }
};
