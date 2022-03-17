<?php

use App\Models\Airfield\Airfield;
use App\Models\Airfield\VisualReferencePoint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAirfieldVisualReferencePointTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('airfield_visual_reference_point', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('airfield_id');
            $table->foreignIdFor(VisualReferencePoint::class);

            $table->foreign('airfield_id')->references('id')->on('airfield');
            $table->foreign('visual_reference_point_id', 'airfield_visual_reference_vrp')->references('id')->on('visual_reference_points');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('airfield_visual_reference_point');
    }
}
