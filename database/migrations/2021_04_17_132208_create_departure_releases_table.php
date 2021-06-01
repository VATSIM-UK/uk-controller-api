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
        Schema::create('departure_release_requests', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->comment('The callsign that the release pertains to');
            $table->unsignedInteger('user_id')->comment('The user triggering the release, for audit purposes');
            $table->unsignedBigInteger('controller_position_id')
                ->comment('The controller position being used to request release');
            $table->unsignedBigInteger('target_controller_position_id')
                ->comment('The controller that is the target of the release');
            $table->timestamp('created_at')->comment('When the release was triggered');
            $table->timestamp('expires_at')->comment('When the release request expires');
            $table->softDeletes();
            $table->unsignedInteger('acknowledged_by')->nullable()->comment('Who acknowledged the release');
            $table->timestamp('acknowledged_at')->nullable()->comment('What time the release was acknowledged');
            $table->unsignedInteger('released_by')->nullable()->comment('Who gave the release');
            $table->timestamp('release_valid_from')->nullable()->comment('What time the release is valid from');
            $table->timestamp('released_at')->nullable()->comment('What time the release was given');
            $table->timestamp('release_expires_at')->nullable()->comment('When the release expired');
            $table->unsignedInteger('rejected_by')->nullable()->comment('Who rejected the release');
            $table->timestamp('rejected_at')->nullable()->comment('When the release was rejected');

            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
            $table->foreign('controller_position_id', 'departure_release_controller_position')
                ->references('id')
                ->on('controller_positions')
                ->cascadeOnDelete();

            $table->foreign('target_controller_position_id', 'departure_release_target_position')
                ->references('id')
                ->on('controller_positions')
                ->cascadeOnDelete();

            $table->foreign('acknowledged_by', 'departure_release_request_acknowledged_by')
                ->references('id')
                ->on('user')
                ->cascadeOnDelete();
            $table->foreign('released_by', 'departure_release_request_released_by')
                ->references('id')
                ->on('user')
                ->cascadeOnDelete();
            $table->foreign('rejected_by', 'departure_release_request_rejected_by')
                ->references('id')
                ->on('user')
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
        Schema::dropIfExists('departure_release_requests');
    }
}
