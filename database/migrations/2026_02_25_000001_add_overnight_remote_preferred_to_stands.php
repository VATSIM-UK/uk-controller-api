<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table): void {
            $table->boolean('overnight_remote_preferred')
                ->default(false)
                ->after('assignment_priority');
        });
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table): void {
            $table->dropColumn('overnight_remote_preferred');
        });
    }
};
