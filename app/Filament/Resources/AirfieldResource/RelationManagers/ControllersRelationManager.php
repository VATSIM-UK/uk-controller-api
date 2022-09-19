<?php

namespace App\Filament\Resources\AirfieldResource\RelationManagers;

use App\Filament\Resources\RelationManagers\AbstractControllersRelationManager;

class ControllersRelationManager extends AbstractControllersRelationManager
{
    function getTableDescription(): ?string
    {
        return self::translateTablePath('table.description');
    }

    public static function getTitle(): string
    {
        return self::translateTablePath('table.title');
    }

    protected static function translationPathRoot(): string
    {
        return 'airfields.controller_positions';
    }
}
