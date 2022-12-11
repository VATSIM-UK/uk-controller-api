<?php

use App\Models\IntentionCode\FirExitPoint;
use App\Models\IntentionCode\IntentionCode;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        IntentionCode::all()
            ->each(function (IntentionCode $intentionCode) {
                if ($intentionCode->conditions[0]['type'] !== 'exit_point') {
                    return;
                }

                FirExitPoint::firstOrCreate(
                    ['exit_point' => $intentionCode->conditions[0]['exit_point']],
                    [
                        'exit_direction_start' => $intentionCode->conditions[0]['exit_direction']['start'],
                        'exit_direction_end' => $intentionCode->conditions[0]['exit_direction']['end'],
                        'internal' => $this->isInternal($intentionCode),
                    ],
                );
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

    private function isInternal(IntentionCode $intentionCode): bool
    {
        return strpos(json_encode($intentionCode->conditions), 'controller_position_starts_with') !== false;
    }
};
