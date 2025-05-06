<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('smr_area', function (Blueprint $table) {
            $table->id()->primary();
            $table->unsignedInteger('airfield_id');
            $table->string('name')->nullable();
            $table->string('source')->nullable();
            $table->text('coordinates');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->timestamps();

            $table->foreign('airfield_id')
                ->references('id')
                ->on('airfield')
                ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('smr_area');
    }
};
