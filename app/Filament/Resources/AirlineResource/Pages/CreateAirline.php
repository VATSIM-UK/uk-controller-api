<?php

namespace App\Filament\Resources\AirlineResource\Pages;

use App\Events\Airline\AirlinesUpdatedEvent;
use App\Filament\Resources\AirlineResource;
use App\Models\Airfield\Terminal;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Support\Facades\DB;

class CreateAirline extends CreateRecord
{
    public static string $resource = AirlineResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return DB::transaction(function () use ($data): Airline {
            return tap(
                static::getModel()::create($data),
                function (Airline $newAirline) use ($data): void {
                    if (!$data['copy_stand_assignments']) {
                        return;
                    }

                    // They've selected an airline to copy assignments from, grab the airline
                    $copyFromAirline = Airline::with('stands', 'terminals')
                        ->findOrFail($data['copy_stand_assignments']);

                    // Sync the terminal assignments
                    if ($copyFromAirline->terminals->isNotEmpty()) {
                        $newAirline->terminals()->sync(
                            $copyFromAirline->terminals->mapWithKeys(
                                fn (Terminal $terminal) => [
                                    $terminal->id => $this->getCopyablePivotAttributes('terminal_id', $terminal->pivot)
                                ]
                            )
                        );
                    }

                    // Sync the stand assignments
                    if ($copyFromAirline->stands->isNotEmpty()) {
                        $newAirline->stands()->sync(
                            $copyFromAirline->stands->mapWithKeys(
                                fn (Stand $stand) => [
                                    $stand->id => $this->getCopyablePivotAttributes('stand_id', $stand->pivot)
                                ]
                            )
                        );
                    }
                }
            );
        });
    }

    private function getCopyablePivotAttributes(string $localModelColumn, Pivot $pivot): array
    {
        $keysToRemove = array_merge(
            [
                'id',
                'created_at',
                'updated_at',
                'airline_id',
            ],
            [
                $localModelColumn
            ]
        );

        return array_filter(
            $pivot->getAttributes(),
            fn (mixed $value, string $key) => !in_array($key, $keysToRemove),
            ARRAY_FILTER_USE_BOTH
        );
    }

    protected function afterCreate(): void
    {
        event(new AirlinesUpdatedEvent);
    }
}
