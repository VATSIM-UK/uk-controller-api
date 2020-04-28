<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSrdNoteSrdRouteTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srd_note_srd_route', function (Blueprint $table) {
            $table->unsignedSmallInteger('srd_note_id');
            $table->unsignedSmallInteger('srd_route_id');

            $table->foreign('srd_note_id')->references('id')->on('srd_notes')->onDelete('cascade');
            $table->foreign('srd_route_id')->references('id')->on('srd_routes')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('srd_note_srd_route');
    }
}
