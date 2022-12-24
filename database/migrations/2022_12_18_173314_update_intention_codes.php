<?php

use App\Models\IntentionCode\FirExitPoint;
use App\Models\IntentionCode\IntentionCode;
use App\Services\IntentionCode\IntentionCodeService;
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
        // LARGA - K3
        $larga = FirExitPoint::create(
            [
                'exit_point' => 'LARGA',
                'internal' => false,
                'exit_direction_start' => 0,
                'exit_direction_end' => 145,
            ]
        );

        $k3 = new IntentionCode(
            [
                'description' => 'K3 (LARGA)',
                'code' => [
                    'type' => 'single_code',
                    'code' => 'K3',
                ],
                'conditions' => [
                    [
                        'type' => 'exit_point',
                        'exit_point' => $larga->id,
                    ]
                ],
                'priority' => 64,
            ]
        );
        IntentionCodeService::saveIntentionCode($k3);

        // KOKSY D1 (Low)
        $d1Low = IntentionCode::findOrFail(22);
        $newConditions = $d1Low->conditions;
        $newConditions[] = ['type' => 'maximum_cruising_level', 'level' => 24000];
        $d1Low->conditions = $newConditions;
        $d1Low->save();

        // KOKSY D (Fallback)
        $dHigh = IntentionCode::findOrFail(23);
        $dHigh['conditions'] = [['type' => 'exit_point', 'exit_point' => 15]];
        $dHigh->save();

        // TOPPA and GODOS directions
        FirExitPoint::whereIn('exit_point', ['TOPPA', 'GODOS'])
            ->update(['exit_direction_end' => 180]);

        // F-code changes
        IntentionCode::findOrFail(46)
            ->update(['code' => ['type' => 'single_code', 'code' => 'F3'], 'description' => 'F3 (LONAM)']);

        IntentionCode::findOrFail(55)
            ->update(['code' => ['type' => 'single_code', 'code' => 'F2'], 'description' => 'F2 (TOPPA)']);

        IntentionCode::findOrFail(58)
            ->update(['code' => ['type' => 'single_code', 'code' => 'F1'], 'description' => 'F1 (GODOS)']);


        // Reneq
        $reneq = FirExitPoint::create(
            [
                'exit_point' => 'RENEQ',
                'internal' => false,
                'exit_direction_start' => 25,
                'exit_direction_end' => 155,
            ]
        );

        $reneqCode = new IntentionCode(
            [
                'description' => 'F4 (RENEQ)',
                'code' => [
                    'type' => 'single_code',
                    'code' => 'F4',
                ],
                'conditions' => [
                    [
                        'type' => 'exit_point',
                        'exit_point' => $reneq->id,
                    ]
                ],
                'priority' => 60,
            ]
        );
        IntentionCodeService::saveIntentionCode($reneqCode);

        // Irish codes directions
        FirExitPoint::whereIn('exit_point', ['MOLAK', 'NIPIT', 'ERNAN', 'DEGOS', 'NIMAT', 'NEVRI', 'RUBEX'])
            ->update(['exit_direction_start' => 180]);

        // ROTEV
        $rotev = FirExitPoint::where('exit_point', 'NEVRI')
            ->firstOrFail()
            ->replicate();

        $rotev->exit_point = 'ROTEV';
        $rotev->save();

        $rotevCode = IntentionCode::findOrFail(114)->replicate();
        $newConditions = $rotevCode->conditions;
        $newConditions[0]['exit_point'] = $rotev->id;
        $rotevCode->conditions = $newConditions;
        $rotevCode->description = 'G7 (ROTEV)';
        IntentionCodeService::saveIntentionCode($rotevCode);
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
