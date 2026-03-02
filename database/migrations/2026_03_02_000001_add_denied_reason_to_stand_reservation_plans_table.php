<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stand_reservation_plans', function (Blueprint $table) {
            $table->text('denied_reason')->nullable()->after('denied_by');
        });
    }

    public function down(): void
    {
        Schema::table('stand_reservation_plans', function (Blueprint $table) {
            $table->dropColumn('denied_reason');
        });
    }
};
