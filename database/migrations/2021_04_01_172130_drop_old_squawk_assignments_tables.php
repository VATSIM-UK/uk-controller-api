<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DropOldSquawkAssignmentsTables extends Migration
{
    const TABLES = [
        'AIRFIELD_PAIR' => 'airfield_pairing_squawk_assignments',
        'CCAMS' => 'ccams_squawk_assignments',
        'ORCAM' => 'orcam_squawk_assignments',
        'UNIT_DISCRETE' => 'unit_discrete_squawk_assignments',
    ];

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->transferSquawks();
        $this->dropTables();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }

    private function dropTables(): void
    {
        foreach (self::TABLES as $table) {
            Schema::dropIfExists($table);
        }
    }

    private function transferSquawks()
    {
        $squawks = new Collection();
        foreach (self::TABLES as $type => $table) {
            $squawks = $squawks->merge($this->getAssignmentsToInsertFromTable($type, $table));
        }

        if ($squawks->isEmpty()) {
            return;
        }

        $squawks = $squawks->unique('code');
        $squawks = $squawks->unique('callsign');

        DB::table('squawk_assignments')->insert($squawks->toArray());
    }

    private function getAssignmentsToInsertFromTable(string $type, string $table)
    {
        return DB::table($table)->get()->map(function ($assignment) use ($type) {
            return [
                'callsign' => $assignment->callsign,
                'code' => $assignment->code,
                'assignment_type' => $type,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ];
        });
    }
}
