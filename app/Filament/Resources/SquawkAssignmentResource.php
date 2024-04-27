<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SquawkAssignmentResource\Pages;
use App\Models\Squawk\SquawkAssignment;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class SquawkAssignmentResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = SquawkAssignment::class;
    protected static ?string $navigationIcon = 'heroicon-o-wifi';
    protected static ?string $navigationGroup = 'Enroute';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignment_type')
                    ->label(self::translateTablePath('columns.type'))
                    ->formatStateUsing(fn (string $state) => self::mapAssignmentTypeToString($state)),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSquawkAssignments::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'squawks.assignments';
    }

    protected static function mapAssignmentTypeToString(string $type): string
    {
        return match ($type) {
            'NON_UKCP' => 'Not assigned by UKCP',
            'AIRFIELD_PAIR' => 'Airfield pairing',
            'CCAMS' => 'CCAMS',
            'ORCAM' => 'ORCAM',
            'UNIT_DISCRETE' => 'ATC unit discrete',
            default => $type
        };
    }
}
