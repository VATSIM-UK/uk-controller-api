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
        Schema::create('plugin_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type')->index()->comment('The type of log, for easy searching');
            $table->text('message')->comment('The message of the log');
            $table->json('metadata')->nullable()->comment('The context of the log');
            $table->timestamp('created_at')->comment('The time the log was created');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plugin_logs');
    }
};
