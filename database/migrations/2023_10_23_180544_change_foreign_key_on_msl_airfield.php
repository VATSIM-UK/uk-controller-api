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
        Schema::table('msl_airfield', function (Blueprint $table): void {
            $table->dropForeign('msl_airfield_airfield_id_foreign');
            $table->foreign('airfield_id')->references('id')->on('airfield')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('msl_airfield', function (Blueprint $table): void {
            $table->dropForeign('msl_airfield_airfield_id_foreign');
            $table->foreign('airfield_id')->references('id')->on('airfield');
        });
    }
};
