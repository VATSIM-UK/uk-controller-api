<?php

namespace App\Filament\Resources\HandoffResource\RelationManagers;

use App\Filament\Resources\RelationManagers\AbstractControllersRelationManager;

class ControllersRelationManager extends AbstractControllersRelationManager
{
    protected static function translationPathRoot(): string
    {
        return 'handoffs';
    }
}
