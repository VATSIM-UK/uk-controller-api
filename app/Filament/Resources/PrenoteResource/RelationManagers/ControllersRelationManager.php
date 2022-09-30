<?php

namespace App\Filament\Resources\PrenoteResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\RelationManagers\AbstractControllersRelationManager;

class ControllersRelationManager extends AbstractControllersRelationManager
{
    use LimitsTableRecordListingOptions;

    protected static function translationPathRoot(): string
    {
        return 'prenotes.controller_positions';
    }
}
