<?php

namespace App\Filament\Resources\SquawkAssignments;

use App\Filament\Resources\SquawkAssignments\Pages\ListSquawkAssignments;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Squawk\SquawkAssignment;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SquawkAssignmentResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = SquawkAssignment::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    protected static string|\UnitEnum|null $navigationGroup = 'Enroute';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('Search by callsign...'),
                TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('assignment_type')
                    ->label(self::translateTablePath('columns.type'))
                    ->formatStateUsing(fn (string $state) => self::mapAssignmentTypeToString($state))
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('assignment_type')
                    ->options([
                        'NON_UKCP' => 'Not assigned by UKCP',
                        'AIRFIELD_PAIR' => 'Airfield pairing',
                        'CCAMS' => 'CCAMS',
                        'ORCAM' => 'ORCAM',
                        'UNIT_DISCRETE' => 'ATC unit discrete',
                    ])
                    ->label(self::translateTablePath('columns.type')),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSquawkAssignments::route('/'),
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
