<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNavaidTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('navaids', function (Blueprint $table) {
            $table->enum('type', ['VOR', 'NDB', 'FIX'])
                ->after('identifier')
                ->comment('The type of the navaid, e.g. VOR, NDB');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('navaids', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
