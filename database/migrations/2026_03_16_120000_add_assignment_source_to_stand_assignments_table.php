<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stand_assignments', function (Blueprint $table): void {
            $table->string('assignment_source')
                ->default('system_auto')
                ->after('stand_id');
        });
    }

    public function down(): void
    {
        Schema::table('stand_assignments', function (Blueprint $table): void {
            $table->dropColumn('assignment_source');
        });
    }
};
