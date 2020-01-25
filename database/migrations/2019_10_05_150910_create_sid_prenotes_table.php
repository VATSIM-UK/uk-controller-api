<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSidPrenotesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sid_prenotes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('sid_id')->comment('The sid for the prenote');
            $table->unsignedBigInteger('prenote_id')->comment('The prenote');
            $table->timestamps();

            // Foreign keys
            $table->unique(['sid_id', 'prenote_id']);
            $table->foreign('sid_id')
                ->references('id')
                ->on('sid')
                ->onDelete('cascade');

            $table->foreign('prenote_id')
                ->references('id')
                ->on('prenotes')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sid_prenotes');
    }
}
