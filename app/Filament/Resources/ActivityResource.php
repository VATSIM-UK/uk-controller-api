<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActivityResource\Pages\ListActivities;
use Z3d0X\FilamentLogger\Resources\ActivityResource as BaseResource;
use Z3d0X\FilamentLogger\Resources\ActivityResource\Pages\ViewActivity;

class ActivityResource extends BaseResource
{
    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }

    public static function getPages(): array
    {
        return [
            'index' => ListActivities::route('/'),
            'view' => ViewActivity::route('/{record}'),
        ];
    }
}
