<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateControllerPositionDepartureReleaseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('controller_position_departure_release_request', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('controller_position_id')
                ->comment('The controller position the release has been requested from');
            $table->unsignedBigInteger('departure_release_request_id')
                ->comment('The release that is being requested');
            $table->unsignedInteger('released_by')->nullable()->comment('Who gave the release');
            $table->timestamp('released_at')->nullable()->comment('What time the release was given');
            $table->timestamp('release_expires_at')->nullable()->comment('When the release expired');
            $table->timestamp('rejected_at')->nullable()->comment('When the release was rejected');

            $table->foreign('released_by', 'departure_release_request_released_by')
                ->references('id')
                ->on('user')
                ->cascadeOnDelete();
            $table->unique(['controller_position_id', 'departure_release_request_id'], 'controller_release_request_unique');
            $table->foreign('controller_position_id', 'departure_release_requested_controller')
                ->references('id')
                ->on('controller_positions')
                ->cascadeOnDelete();

            $table->foreign('departure_release_request_id', 'departure_release_request_release')
                ->references('id')
                ->on('departure_release_requests')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('controller_position_departure_release_request');
    }
}
