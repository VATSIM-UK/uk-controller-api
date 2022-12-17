<?php

namespace App\Services\IntentionCode;

use App\Models\IntentionCode\IntentionCode;
use Illuminate\Support\Facades\DB;

class IntentionCodeService
{
    public function getIntentionCodesDependency(): array
    {
        return IntentionCode::orderBy('priority')
            ->get()
            ->toArray();
    }

    public static function saveIntentionCode(IntentionCode $code, ?int $previousPriority = null): void
    {
        DB::transaction(function () use ($code, $previousPriority)
        {
            if ($code->priority === $previousPriority) {
                $code->save();
                return;
            }

            // If its not an existing position being updated, we just move things up one.
            if (!$previousPriority) {
                IntentionCode::where('priority', '>=', $code->priority)
                    ->orderByDesc('priority')
                    ->each(
                        function (IntentionCode $code)
                        {
                            $code->update(['priority' => $code->priority + 1]);
                        }
                    );

                $code->save();
                return;
            }

            // Record the intented priority, and move our code out of the way
            $intendedPriority = $code->priority;
            $code->priority = 99999;
            $code->save();

            if ($intendedPriority > $previousPriority) {
                // Move everything above the current position, but below or equal to the intended position, down one
                IntentionCode::where('priority', '<=', $intendedPriority)
                    ->where('priority', '>', $previousPriority)
                    ->orderBy('priority')
                    ->each(
                        function (IntentionCode $code)
                        {
                            $code->update(['priority' => $code->priority - 1]);
                        }
                    );
            } else {
                // Move everything below the current position, but above or equal to the intended position, up one
                IntentionCode::where('priority', '>=', $intendedPriority)
                    ->where('priority', '<', $previousPriority)
                    ->orderByDesc('priority')
                    ->each(
                        function (IntentionCode $code)
                        {
                            $code->update(['priority' => $code->priority + 1]);
                        }
                    );
            }

            // Finally, put the code where it's supposed to be
            $code->priority = $intendedPriority;
            $code->save();
        });
    }
}
