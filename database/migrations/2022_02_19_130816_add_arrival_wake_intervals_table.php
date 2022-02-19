<?php

use App\Models\Aircraft\WakeCategory;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddArrivalWakeIntervalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arrival_wake_intervals', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('lead_wake_category_id')->comment('The wake category in the lead');
            $table->unsignedTinyInteger('following_wake_category_id')->comment('The wake category in the lead');

            $table->foreign('lead_wake_category_id')->references('id')->on('wake_categories')->cascadeOnDelete();
            $table->foreign('following_wake_category_id')->references('id')->on('wake_categories')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arrival_wake_intervals');
    }
}
