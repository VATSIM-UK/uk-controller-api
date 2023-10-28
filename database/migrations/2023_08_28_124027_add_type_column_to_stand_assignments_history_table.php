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
        Schema::table('stand_assignments_history', function (Blueprint $table) {
            $table->string('type')
                ->default('unknown')
                ->index()
                ->after('stand_id')
                ->comment('The type of assignment, e.g. the rule name, departure');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stand_assignments_history', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
