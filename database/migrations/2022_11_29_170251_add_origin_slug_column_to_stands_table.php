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
        Schema::table('stands', function (Blueprint $table) {
            $table->string('origin_slug', 4)
                ->nullable()
                ->index()
                ->comment('A partial ICAO match for the origin airfield that can be used when allocating this stand')
                ->after('type_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropIndex('stands_origin_slug_index');
            $table->dropColumn('origin_slug');
        });
    }
};
