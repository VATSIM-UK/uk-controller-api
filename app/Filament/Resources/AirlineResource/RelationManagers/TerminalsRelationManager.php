<?php

namespace App\Filament\Resources\AirlineResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Airfield\Terminal;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Support\Facades\DB;

class TerminalsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    private const DEFAULT_COLUMN_VALUE = '--';
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'terminals';
    protected static ?string $inverseRelationship = 'airlines';
    protected static ?string $recordTitleAttribute = 'description';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('terminal')
                    ->formatStateUsing(fn (Terminal $record) => sprintf('%s / %s', $record->airfield->code, $record->description))
                    ->label(self::translateTablePath('columns.terminal'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination'))
                    ->default(self::DEFAULT_COLUMN_VALUE)
                    ->sortable(),
                Tables\Columns\TextColumn::make('callsign_slug')
                    ->default(self::DEFAULT_COLUMN_VALUE)
                    ->label(self::translateTablePath('columns.callsign')),
                Tables\Columns\TextColumn::make('priority')
                    ->default(self::DEFAULT_COLUMN_VALUE)
                    ->label(self::translateTablePath('columns.priority'))
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-terminal')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Terminal $record):string => $record->airfieldDescription)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        TextInput::make('destination')
                            ->label(self::translateFormPath('destination.label'))
                            ->helperText(self::translateFormPath('destination.helper'))
                            ->maxLength(4),
                        TextInput::make('callsign_slug')
                            ->label(self::translateFormPath('callsign.label'))
                            ->helperText(self::translateFormPath('callsign.helper'))
                            ->maxLength(4),
                        TextInput::make('priority')
                            ->label(self::translateFormPath('priority.label'))
                            ->helperText(self::translateFormPath('priority.helper'))
                            ->default(100)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->required(),
                    ])
            ])
            ->actions([
                DetachAction::make('unpair-terminal')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(function (DetachAction $action) {
                        DB::table('airline_terminal')
                            ->where('id', $action->getRecord()->pivot_id)
                            ->delete();
                    })
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines.terminals';
    }
}
