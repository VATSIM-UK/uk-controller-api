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
                UnitDiscreteSquawkRange::findOrFail($rule->unit_discrete_squawk_range_id)->update(['rule' => json_decode($rule->rule)]);
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
