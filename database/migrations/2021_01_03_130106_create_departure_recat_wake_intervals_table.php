<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureRecatWakeIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_recat_wake_intervals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('lead_recat_category_id')
                ->comment('The recat category of the lead aircraft');
            $table->unsignedBigInteger('following_recat_category_id')
                ->comment('The recat category of the following aircraft');
            $table->unsignedInteger('interval')->comment('The value of the interval in seconds');

            $table->foreign('lead_recat_category_id', 'recat_lead')
                ->references('id')
                ->on('recat_categories')
                ->cascadeOnDelete();
            $table->foreign('following_recat_category_id', 'recat_follow')
                ->references('id')
                ->on('recat_categories')
                ->cascadeOnDelete();
            $table->unique(
                ['lead_recat_category_id', 'following_recat_category_id', 'intermediate'],
                'lead_following_recat_intermediate'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('departure_recat_wake_intervals');
    }
}
