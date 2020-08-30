<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnrouteReleaseHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enroute_releases', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('enroute_release_type_id')->comment('The type of release');
            $table->string('callsign')->comment('Which aircraft was the release for');
            $table->string('initiating_controller')->comment('Who initiated the release');
            $table->string('target_controller')->comment('Who was the target of the release');
            $table->string('release_point', 15)
                ->nullable()
                ->comment(
                    'The point at which the release takes effect. Limited to 15 characters as max length of tag item.'
                );
            $table->unsignedInteger('user_id')->comment('Which user initiated the release');
            $table->timestamp('released_at');

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
        Schema::dropIfExists('enroute_releases');
    }
}
