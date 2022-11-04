<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('unit_conspicuity_squawk_codes', function (Blueprint $table) {
            $table->json('rules')
                ->nullable()
                ->after('code')
                ->comment('Rules for applying this range');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('unit_conspicuity_squawk_codes', function (Blueprint $table) {
            //
        });
    }
};
