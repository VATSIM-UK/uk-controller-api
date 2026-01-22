<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('stand_types')
            ->insert(
                [
                    [
                        'key' => 'BUSINESS AVIATION',
                        'created_at' => Carbon::now(),
                    ],
                ]
            );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('stand_types')
            ->where('key', 'BUSINESS AVIATION')
            ->delete();
    }
};
