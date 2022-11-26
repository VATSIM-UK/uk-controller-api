<?php

use App\Models\Squawk\UnitDiscrete\UnitDiscreteSquawkRange;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('unit_discrete_squawk_range_rules')
            ->get()
            ->map(function (object $rule): void {
                $range = UnitDiscreteSquawkRange::findOrFail($rule->unit_discrete_squawk_range_id);
                $range->update(['rules' => array_merge($range->rules ?? [], [json_decode($rule->rule)])]);
            });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
};
