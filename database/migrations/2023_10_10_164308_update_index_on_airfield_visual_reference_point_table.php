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
        Schema::table('airfield_visual_reference_point', function (Blueprint $table)
        {
            $table->dropForeign('airfield_visual_reference_point_airfield_id_foreign');
            $table->foreign('airfield_id')->references('id')->on('airfield')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
