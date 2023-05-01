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
        Schema::table('user', function (Blueprint $table) {
            $table->boolean('stand_acars_messages_uncontrolled_airfield')
                ->default(false)
                ->comment(
                    'Whether the user wants to be sent acars messages related to stand assignments at uncontrolled ' .
                    'airfields'
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user', function (Blueprint $table) {
            $table->dropColumn('stand_acars_messages_uncontrolled_airfield');
        });
    }
};
