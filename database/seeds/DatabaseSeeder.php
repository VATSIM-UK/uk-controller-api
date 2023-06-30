<?php

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    // The seeders and the tables they affect
    const SEEDERS = [
        VersionTableSeeder::class => [
            'version',
        ],
        UserTableSeeder::class => [
            'user',
            'role_user',
        ],
        AsrTableSeeder::class => [
            'altimeter_setting_region',
            'altimeter_setting_region_airfield',
            'regional_pressure_settings',
        ],
        AirfieldTableSeeder::class => [
            'airfield',
            'airfield_pairing_prenotes',
            'msl_calculation_airfields',
            'metars',
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
            'deemed_separated_holds',
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
            'database_table_dependency',
        ],
        AircraftTableSeeder::class => [
            'aircraft',
            'aircraft_wake_category',
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
            'navaid_network_aircraft',
        ],
        NetworkAircraftTableSeeder::class => [
            'network_aircraft',
        ],
        NotificationTableSeeder::class => [
            'notifications',
            'notification_user',
            'controller_position_notification',
        ],
        AssignedHoldsTableSeeder::class => [
            'assigned_holds',
            'assigned_holds_history',
        ],
        SquawkRangeTablesSeeder::class => [
            'ccams_squawk_ranges',
            'orcam_squawk_ranges',
            'airfield_pairing_squawk_ranges',
            'unit_discrete_squawk_ranges',
            'unit_conspicuity_squawk_codes',
        ],
        SquawkAssignmentTablesSeeder::class => [
            'squawk_assignments',
            'squawk_assignments_history',
        ],
        StandTableSeeder::class => [
            'stands',
            'stand_assignments',
            'stand_assignments_history',
            'stand_pairs',
            'stand_reservations',
            'aircraft_stand',
        ],
        TerminalTableSeeder::class => [
            'terminals',
        ],
        AirlineTableSeeder::class => [
            'airlines',
            'airline_stand',
            'airline_terminal',
        ],
        DepartureIntervalTableSeeder::class => [
            'sid_departure_interval_groups',
            'sid_departure_interval_group_sid_departure_interval_group',
        ],
        PluginEventTableSeeder::class => [
            'plugin_events',
        ],
        DepartureReleaseTableSeeder::class => [
            'departure_release_requests',
        ],
        DatabaseTableSeeder::class => [
            'database_tables',
        ],
        RunwayTableSeeder::class => [
            'runways',
            'runway_runway',
        ],
        VrpTableSeeder::class => [
            'visual_reference_points',
            'airfield_visual_reference_point',
        ],
        FirExitPointSeeder::class => [
            'fir_exit_points',
        ],
        IntentionCodeSeeder::class => [
            'intention_codes',
        ],
    ];

    const OTHER_TABLES_TO_TRUNCATE = [
        'metars',
        'missed_approach_notifications',
        'network_controller_positions',
        'controller_position_alternative_callsigns',
        'acars_messages',
        'stand_requests',
        'stand_request_history',
    ];


    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement("SET foreign_key_checks=0");
        // Truncate all tables
        foreach (self::SEEDERS as $tables) {
            foreach ($tables as $table) {
                DB::table($table)->truncate();
            }
        }

        foreach (self::OTHER_TABLES_TO_TRUNCATE as $table) {
            DB::table($table)->truncate();
        }

        // Seed tables
        foreach (self::SEEDERS as $seeder => $tables) {
            $this->call($seeder);
        }
        DB::statement("SET foreign_key_checks=1");
    }
}
