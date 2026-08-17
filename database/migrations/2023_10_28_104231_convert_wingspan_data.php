<?php

use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('aircraft')->update([
            'wingspan' => DB::raw('wingspan / 3.28084'),
            'length' => DB::raw('length / 3.28084'),
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
