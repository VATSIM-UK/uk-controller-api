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
        // Update the srd_routes table to drop the foreign key constraint on the srd_note_srd_route table
        Schema::table('srd_note_srd_route', function (Blueprint $table) {
            $table->dropForeign('srd_note_srd_route_srd_note_id_foreign');
        });

        // Update the srd_notes table to make the id column a bigIncrements rather than a smallIncrements
        Schema::table('srd_notes', function (Blueprint $table) {
            $table->bigIncrements('id')->first()->change();
        });

        // Now update the srd_note_srd_route table to make the srd_note_id column a bigInteger rather than an integer
        // and then re-add the foreign key constraint
        Schema::table('srd_note_srd_route', function (Blueprint $table) {
            $table->unsignedBigInteger('srd_note_id')->first()->change();
            $table->foreign('srd_note_id')->references('id')->on('srd_notes')->onDelete('cascade');
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
