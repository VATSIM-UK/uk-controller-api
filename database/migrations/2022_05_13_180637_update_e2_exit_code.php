<?php

use App\Models\IntentionCode\IntentionCode;
use App\Services\IntentionCode\Builder\ConditionBuilder;
use App\Services\IntentionCode\Builder\IntentionCodeBuilder;
use App\Services\IntentionCode\Condition\Condition;
use Illuminate\Database\Migrations\Migration;

class UpdateE2ExitCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        IntentionCodeBuilder::from(IntentionCode::findOrFail(30))
            ->withCondition(function (ConditionBuilder $condition) {
                $condition->removeWhere(
                    fn (Condition $condition) => $condition->toArray()['type'] === 'maximum_cruising_level'
                )
                    ->maximumCruisingLevel(29000);
            })
            ->save();
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
}
