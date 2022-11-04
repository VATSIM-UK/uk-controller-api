<?php

use App\Models\Squawk\UnitConspicuity\UnitConspicuitySquawkCode;
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
        DB::table('unit_conspicuity_squawk_rules')
            ->get()
            ->map(function (object $rule): void {
                $code = UnitConspicuitySquawkCode::findOrFail($rule->unit_conspicuity_squawk_code_id);
                $code->update(['rules' => array_merge($code->rules ?? [], [json_decode($rule->rule)])]);
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
