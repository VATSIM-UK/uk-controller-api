<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDepartureWakeIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('departure_wake_intervals', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('lead_wake_category_id')
                ->comment('The wake category of the lead aircraft');
            $table->unsignedTinyInteger('following_wake_category_id')
                ->comment('The wake category of the following aircraft');
            $table->unsignedInteger('value')->comment('The value of the interval in seconds');
            $table->boolean('intermediate')->comment('Whether its from an intermediate point on the runway');

            $table->foreign('lead_wake_category_id')->references('id')->on('wake_categories')->cascadeOnDelete();
            $table->foreign('following_wake_category_id')->references('id')->on('wake_categories')->cascadeOnDelete();
            $table->unique(
                ['lead_wake_category_id', 'following_wake_category_id', 'intermediate'],
                'lead_following_category_intermediate'
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
        Schema::dropIfExists('departure_wake_intervals');
    }
}
