<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('stand_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('user_id');
            $table->unsignedBigInteger('stand_id');
            $table->string('callsign');
            $table->timestamp('from');
            $table->timestamp('to');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('stand_id')
                ->references('id')
                ->on('stands')
                ->cascadeOnDelete();

            $table->foreign('user_id')
                ->references('id')
                ->on('user')
                ->cascadeOnDelete();

            $table->index(['user_id', 'from', 'to']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stand_requests');
    }
};
