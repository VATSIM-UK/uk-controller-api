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
        Schema::table('stand_assignments_history', function (Blueprint $table)
        {
            $table->json('context')
                ->default('{}')
                ->nullable()
                ->after('user_id')
                ->comment('Contextual information about the assignment');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stand_assignments_history', function (Blueprint $table)
        {
            $table->dropColumn('context');
        });
    }
};
