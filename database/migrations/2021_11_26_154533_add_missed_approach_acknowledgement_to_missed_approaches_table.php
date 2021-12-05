<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissedApproachAcknowledgementToMissedApproachesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('missed_approach_notifications', function (Blueprint $table) {
            $table->unsignedInteger('acknowledged_by')->nullable()->comment('Who acknowledged the missed approach');
            $table->timestamp('acknowledged_at')->nullable()->comment('When the missed approach was acknowledged');
            $table->string('remarks', 500)
                ->nullable()
                ->comment('Any remarks or instructions from the acknowledging controller');

            $table->foreign('acknowledged_by')->references('id')->on('user')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('missed_approach_notifications', function (Blueprint $table) {
            $table->dropColumn('acknowledged_by');
            $table->dropColumn('acknowledged_at');
            $table->dropColumn('remarks');
        });
    }
}
