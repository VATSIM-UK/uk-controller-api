<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMissedApproachNotificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('missed_approach_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->index()->comment('The callsign the notification relates to');
            $table->unsignedInteger('user_id')->comment('Who sent the notification');
            $table->timestamp('expires_at')->index()->comment('When the notification expires');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('missed_approach_notifications');
    }
}
