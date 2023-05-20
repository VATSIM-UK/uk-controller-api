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
        Schema::table('stands', function (Blueprint $table) {
            $table->dropForeign('stands_wake_category_id_foreign');
            $table->dropColumn('wake_category_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};
