<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('stand_reservation_plans', function (Blueprint $table) {
            $table->timestamp('denied_at')->nullable()->after('approved_at');
            $table->unsignedInteger('denied_by')->nullable()->after('approved_by');
            $table->foreign('denied_by')->references('id')->on('user')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('stand_reservation_plans', function (Blueprint $table) {
            $table->dropForeign(['denied_by']);
            $table->dropColumn('denied_by');
            $table->dropColumn('denied_at');
        });
    }
};
