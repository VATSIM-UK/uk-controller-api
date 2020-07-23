<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddReservedSquawkCodeData extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('reserved_squawk_codes')
            ->insert(
                [
                    [
                        'code' => '7000',
                        'description' => 'VFR Conspicuity in the United Kingdom'
                    ],
                    [
                        'code' => '2000',
                        'description' => 'IFR Conspicuity in the United Kingdom'
                    ],
                    [
                        'code' => '2200',
                        'description' => 'Often used VATSIM non-discrete code',
                    ],
                    [
                        'code' => '1200',
                        'description' => 'Default code on pilot clients',
                    ],
                ],
            );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('reserved_squawk_codes')->delete();
    }
}
