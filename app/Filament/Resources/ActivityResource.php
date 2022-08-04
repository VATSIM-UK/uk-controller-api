<?php

namespace App\Filament\Resources;

use Z3d0X\FilamentLogger\Resources\ActivityResource as BaseResource;

class ActivityResource extends BaseResource
{
    protected static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }
}
