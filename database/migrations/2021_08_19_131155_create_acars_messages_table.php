<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAcarsMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('acars_messages', function (Blueprint $table) {
            $table->id();
            $table->string('callsign')->index()->comment('Who the target of the message is');
            $table->text('message')->comment('The message itself');
            $table->boolean('successful')->comment('Was the message successfully sent to the server');
            $table->timestamps();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('acars_messages');
    }
}
