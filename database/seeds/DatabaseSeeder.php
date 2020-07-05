<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // The seeders and the tables they affect
    const SEEDERS = [
        VersionTableSeeder::class => [
            'version',
        ],
        UserTableSeeder::class => [
            'user',
        ],
        AdminTableSeeder::class => [
            'admin',
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
            'altimeter_setting_region_airfield',
            'regional_pressure_settings',
        ],
        AirfieldTableSeeder::class => [
            'airfield',
            'airfield_pairing_prenotes',
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
            'holds',
        ],
        HoldProfileTableSeeder::class => [
            'hold_profile',
        ],
        HoldProfileHoldTableSeeder::class => [
            'hold_profile_hold',
        ],
        HoldRestrictionTableSeeder::class => [
            'hold_restrictions',
        ],
        SidTableSeeder::class => [
            'sid',
            'sid_prenotes',
        ],
        DependencyTableSeeder::class => [
            'dependency_user',
            'dependencies',
        ],
        AircraftTableSeeder::class => [
            'aircraft',
            'wake_categories',
        ],
        PrenoteTableSeeder::class => [
            'prenotes',
            'prenote_orders',
        ],
        ControllerPositionsTableSeeder::class => [
            'controller_positions',
            'top_downs',
        ],
        HandoffTableSeeder::class => [
            'handoffs',
            'handoff_orders',
        ],
        SectorFileIssuesTableSeeder::class => [
            'sector_file_issues',
        ],
        SrdTableSeeder::class => [
            'srd_note_srd_route',
            'srd_routes',
            'srd_notes',
        ],
        NavaidTableSeeder::class => [
            'navaids',
        ],
        NetworkAircraftTableSeeder::class => [
            'network_aircraft',
        ],
        AssignedHoldsTableSeeder::class => [
            'assigned_holds',
            'assigned_holds_history'
        ],
        SquawkRangeTablesSeeder::class => [
            'ccams_squawk_ranges',
            'orcam_squawk_ranges',
            'airfield_pairing_squawk_ranges',
            'unit_discrete_squawk_ranges',
        ],
        SquawkAssignmentTablesSeeder::class => [
            'ccams_squawk_assignments',
            'orcam_squawk_assignments',
            'airfield_pairing_squawk_assignments',
            'unit_discrete_squawk_assignments',
        ]
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

        // Seed
        foreach (self::SEEDERS as $seeder => $tables) {
            $this->call($seeder);
        }
        DB::statement("SET foreign_key_checks=1");
    }
}
