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

            $table->index('overnight_remote_preferred');
        });
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table): void {
            $table->dropIndex(['overnight_remote_preferred']);
            $table->dropColumn('overnight_remote_preferred');
        });
    }
};
