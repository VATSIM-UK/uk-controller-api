<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class() extends Migration {
    public function up(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->string('allocation_status')->default('open')->index();
        });

        DB::table('stands')
            ->whereNotNull('closed_at')
            ->update(['allocation_status' => 'unavailable']);
    }

    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropColumn('allocation_status');
        });
    }
};
