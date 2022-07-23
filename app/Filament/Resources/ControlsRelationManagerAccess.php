<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Resources\RelationManagers\RelationManager;
use Illuminate\Support\Facades\Auth;

trait ControlsRelationManagerAccess
{
    private static function canUpdateRelations(): Closure
    {
        return fn(RelationManager $livewire) => $livewire->getOwnerRecord()->id === Auth::id();
    }
}
