<?php

namespace App\Filament\Resources\Navaids\Pages;

use App\Filament\Resources\Navaids\NavaidResource;
use Filament\Pages\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateNavaid extends CreateRecord
{
    protected static string $resource = NavaidResource::class;
}
