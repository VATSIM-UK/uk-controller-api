<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReleaseRemarksToDepartureReleaseRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('departure_release_requests', function (Blueprint $table) {
            $table->string('release_remarks')
                ->nullable()
                ->after('release_expires_at')
                ->comment('Any remarks or other instructions to go with the release');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('departure_release_requests', function (Blueprint $table) {
            $table->dropColumn('release_remarks');
        });
    }
}
