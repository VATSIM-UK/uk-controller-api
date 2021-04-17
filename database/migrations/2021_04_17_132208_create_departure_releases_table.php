<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_releases', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The callsign that the release pertains to');
            $table->unsignedInteger('user_id')->comment('The user triggering the release');
            $table->timestamp('created_at')->comment('When the release was triggered');
            $table->timestamp('expires_at')->comment('When the release request expires');

            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_releases');
    }
}
