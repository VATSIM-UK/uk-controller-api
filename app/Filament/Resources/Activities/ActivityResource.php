<?php

namespace App\Filament\Resources\Activities;

use App\Filament\Resources\Activities\Pages\ListActivities;

use Jacobtims\FilamentLogger\Resources\ActivityResource as BaseResource;
use Jacobtims\FilamentLogger\Resources\ActivityResource\Pages\ViewActivity;

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
