<?php

namespace App\Filament\Resources\AirfieldResource\RelationManagers;

use App\Filament\Resources\RelationManagers\AbstractControllersRelationManager;
use App\Models\Controller\ControllerPosition;
use App\Services\ControllerPositionHierarchyService;
use Illuminate\Database\Eloquent\Model;

class ControllersRelationManager extends AbstractControllersRelationManager
{
    protected static ?string $inverseRelationship = 'topDownAirfields';

    public function getTableDescription(): ?string
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

    protected static function postUpdate(Model $ownerRecord): void
    {
        ControllerPositionHierarchyService::setPositionsForHierarchyByControllerId(
            $ownerRecord->handoff,
            $ownerRecord->controllers
                ->filter(fn (ControllerPosition $position) => $position->isApproach() || $position->isEnroute())
                ->pluck('id')
                ->toArray()
        );
    }
}
