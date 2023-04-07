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
        Schema::table('terminals', function (Blueprint $table) {
            $table->dropUnique('terminals_key_unique');
            $table->dropColumn('key');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // There is no down.
    }
};
