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
        Schema::table('aircraft', function (Blueprint $table) {
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
            )->index()->after('code')->comment(
                'The aerodrome reference code for the aircraft'
            );
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('aircraft', function (Blueprint $table) {
            $table->dropColumn('aerodrome_reference_code');
        });
    }
};
