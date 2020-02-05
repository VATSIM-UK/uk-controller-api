<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // The seeders and the tables they affect
    const SEEDERS = [
        VersionTableSeeder::class => [
            'version'
        ],
        UserTableSeeder::class => [
            'user'
        ],
        AdminTableSeeder::class => [
            'admin'
        ],
        SquawkTableSeeder::class => [
            'squawk_general',
            'squawk_unit',
            'squawk_range_owner',
            'squawk_range',
        ],
        SquawkAllocationTableSeeder::class => [
            'squawk_allocation',
        ],
        AllocationHistorySeeder::class => [
            'squawk_allocation_history',
        ],
        AsrTableSeeder::class => [
            'altimeter_setting_region',
        ],
        AirfieldTableSeeder::class => [
            'airfield',
        ],
        TmaTableSeeder::class => [
            'tma',
        ],
        MslAirfieldTableSeeder::class => [
            'msl_airfield',
        ],
        MslTmaTableSeeder::class => [
            'msl_tma',
        ],
        HoldTableSeeder::class => [
            'hold',
        ],
        HoldProfileTableSeeder::class => [
            'hold_profile',
        ],
        HoldProfileHoldTableSeeder::class => [
            'hold_profile_hold',
        ],
        HoldRestrictionTableSeeder::class => [
            'hold_restriction',
        ],
        SidTableSeeder::class => [
            'sid',
        ],
        SectorFileIssuesTableSeeder::class => [
            'sector_file_issues',
        ],
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate all tables
        DB::statement("SET foreign_key_checks=0");
        foreach (self::SEEDERS as $seeder => $tables) {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        }
        DB::statement("SET foreign_key_checks=1");

        // Seed
        foreach (self::SEEDERS as $seeder => $tables) {
            $this->call($seeder);
        }
    }
}
