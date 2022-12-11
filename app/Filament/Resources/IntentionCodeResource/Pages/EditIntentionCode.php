<?php

namespace App\Filament\Resources\IntentionCodeResource\Pages;

use App\Filament\Resources\IntentionCodeResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditIntentionCode extends EditRecord
{
    protected static string $resource = IntentionCodeResource::class;

    protected function getActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $mutatedConditions = [];
        foreach ($data['conditions'] as $condition) {
            if ($condition['type'] === 'arrival_airfields') {
                $mutatedConditions['airfields'] = $condition['airfields'];
            }
        }

        return [
            'code_type' => $data['code']['type'],
            'single_code' => $data['code']['type'] === 'single_code' ? $data['code']['code'] : null,
            'conditions' => $mutatedConditions,
        ];
    }
}
