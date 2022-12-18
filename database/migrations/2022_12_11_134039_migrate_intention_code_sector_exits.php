<?php

use App\Models\IntentionCode\FirExitPoint;
use App\Models\IntentionCode\IntentionCode;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        IntentionCode::all()
            ->each(function (IntentionCode $intentionCode)
            {
                if ($intentionCode->conditions[0]['type'] === 'exit_point') {
                    $newConditions = $intentionCode->conditions;
                    $newConditions[0] = [
                        'type' => 'exit_point',
                        'exit_point' => FirExitPoint::where('exit_point', $newConditions[0]['exit_point'])->firstOrFail()->id,
                    ];


                    $intentionCode->conditions = $newConditions;
                }

                $intentionCode->description = $intentionCode->code['type'] === 'airfield_identifier'
                    ? 'Home Airfields'
                    : 'Code: ' . $intentionCode->code['code'];

                $intentionCode->save();
            });
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
};
