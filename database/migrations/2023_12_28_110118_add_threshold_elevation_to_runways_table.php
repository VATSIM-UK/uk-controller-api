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
        Schema::table('runways', function (Blueprint $table) {
            $table->mediumInteger('threshold_elevation')
                ->after('threshold_longitude');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('runways', function (Blueprint $table) {
            $table->dropColumn('threshold_elevation');
        });
    }
};
