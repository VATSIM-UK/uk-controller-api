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
            $table->enum(
                'aerodrome_reference_code',
                [
                    'A',
                    'B',
                    'C',
                    'D',
                    'E',
                    'F',
                ]
            )->index()
                ->after('origin_slug')
                ->comment(
                    'The aerodrome reference code for the stand that represents the maximum aircraft size that can be accommodated'
                );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stands', function (Blueprint $table) {
            $table->dropColumn('aerodrome_reference_code');
        });
    }
};
