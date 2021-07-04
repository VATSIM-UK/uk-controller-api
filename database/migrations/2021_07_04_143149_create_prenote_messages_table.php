<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePrenoteMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prenote_messages', function (Blueprint $table) {
            $table->id();
            $table->string('callsign');
            $table->string('departure_airfield', 4)
                ->comment('The airfield the aircraft is departing');
            $table->string('destination_airfield', 4)
                ->nullable()
                ->comment('The airfield the aircraft is arriving to');
            $table->string('departure_sid')
                ->nullable()
                ->comment('The sid the aircraft is departing if applicable');
            $table->unsignedInteger('user_id')->comment('The user triggering the release, for audit purposes');
            $table->unsignedBigInteger('controller_position_id')
                ->comment('The controller position being used to send the prenote');
            $table->unsignedBigInteger('target_controller_position_id')
                ->comment('The controller position that is the target of the prenote');
            $table->timestamp('created_at')->comment('When the release was triggered');
            $table->timestamp('expires_at')->comment('When the release request expires');
            $table->softDeletes();
            $table->unsignedInteger('acknowledged_by')->nullable()->comment('Who acknowledged the release');
            $table->timestamp('acknowledged_at')->nullable()->comment('What time the release was acknowledged');

            $table->foreign('user_id')->references('id')->on('user')->cascadeOnDelete();
            $table->foreign('controller_position_id', 'prenote_message_controller_position')
                ->references('id')
                ->on('controller_positions')
                ->cascadeOnDelete();

            $table->foreign('target_controller_position_id', 'prenote_message_target_position')
                ->references('id')
                ->on('controller_positions')
                ->cascadeOnDelete();

            $table->foreign('acknowledged_by', 'prenote_message_request_acknowledged_by')
                ->references('id')
                ->on('user')
                ->cascadeOnDelete();

            $table->index('deleted_at');
            $table->index('acknowledged_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prenote_messages');
    }
}
