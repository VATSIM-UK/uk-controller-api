<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNotificationReadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notification_reads', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('notification_id');
            $table->unsignedInteger('user_id');
            $table->timestamps();

            // Foreign keys
            $table->foreign('notification_id')
                ->references('id')
                ->on('notifications')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notification_reads');
    }
}
