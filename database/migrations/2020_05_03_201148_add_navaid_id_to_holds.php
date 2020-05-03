<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNavaidIdToHolds extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->unsignedBigInteger('navaid_id')->after('id')->nullable();
            $table->foreign('navaid_id')->references('id')->on('navaids')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('holds', function (Blueprint $table) {
            $table->dropForeign('holds_navaid_id_foreign');
            $table->dropColumn('navaid_id');
        });
    }
}
