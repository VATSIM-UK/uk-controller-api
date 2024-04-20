<?php

namespace App\Filament\Resources\HandoffResource\Pages;

use App\Filament\Resources\HandoffResource;
use App\Models\Controller\ControllerPosition;
use App\Models\Controller\Handoff;
use Filament\Pages\Actions;
use Filament\Resources\Pages\EditRecord;

class EditHandoff extends EditRecord
{
    protected static string $resource = HandoffResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['controllers'] = $this->getRecord()->controllers->map(
            fn (ControllerPosition $controller) => ['controller' => $controller->id]
        )->toArray();

        return $data;
    }
}
