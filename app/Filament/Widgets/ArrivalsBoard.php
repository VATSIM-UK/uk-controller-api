<?php

namespace App\Filament\Widgets;

use App\Models\Vatsim\NetworkAircraft;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ArrivalsBoard extends BaseWidget
{
    protected int | string | array $columnSpan = 1;

    protected function getTableDescription(): ?string
    {
        return 'Stands will be assigned approximately 20 minutes prior to arrival.';
    }

    protected function getTableQuery(): Builder
    {
        return NetworkAircraft::with('assignedStand.stand', 'destinationAirfield')
            ->join('airfield', 'airfield.code', '=', 'network_aircraft.planned_destairport')
            ->leftJoin('stand_assignments', 'stand_assignments.callsign', '=', 'network_aircraft.callsign')
            ->leftJoin('stands', 'stand_assignments.stand_id', '=', 'stands.id')
            ->leftJoin('airfield as stand_airfield', 'stands.airfield_id', '=', 'stand_airfield.id')
            ->where(function (Builder $builder) {
                $builder->whereRaw('`stand_airfield`.`code` = `network_aircraft`.`planned_destairport`')
                    ->orWhereNull('stand_assignments.callsign');
            })
            ->select('network_aircraft.*');
    }
    protected function getDefaultTableSortColumn(): ?string
    {
        return 'network_aircraft.callsign';
    }

    protected function getTableColumns(): array
    {
        return [
            TextColumn::make('callsign')
                ->sortable()
                ->searchable(['network_aircraft.callsign', 'airfield.code']),
            TextColumn::make('destinationAirfield.code')
                ->sortable(),
            TextColumn::make('assignedStand.stand.identifier')
                ->formatStateUsing(fn (?string $state) => $state ?? '--'),
        ];
    }
}
