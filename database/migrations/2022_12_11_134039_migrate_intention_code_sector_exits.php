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
                    : $this->getCodeDescription($intentionCode->code['code'], $intentionCode->conditions);

                $intentionCode->save();
            });
    }

    private function getCodeDescription(string $code, array $conditions): string
    {
        if ($conditions[0]['type'] !== 'exit_point') {
            return $code;
        }

        return $code . ' (' . FirExitPoint::findOrFail($conditions[0]['exit_point'])->exit_point . ')';
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
