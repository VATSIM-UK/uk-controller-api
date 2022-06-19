<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;

class AirlinesRelationManager extends RelationManager
{
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'airlines';
    protected static ?string $inverseRelationship = 'stands';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function canDelete(Model $record): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')
                    ->label(__('ICAO Code'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(__('Destination'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('callsign_slug')
                    ->label(__('Callsign Slug')),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('Allocation Priority')),
                Tables\Columns\TextColumn::make('not_before')
                    ->label(__('Not Before (UTC)')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label('Airline')
                            ->required(),
                        TextInput::make('destination')
                            ->label(__('Destination'))
                            ->maxLength(4),
                        TextInput::make('callsign_slug')
                            ->label(__('Callsign Slug'))
                            ->helperText('Callsign slug to match. Should not include the airline ICAO.')
                            ->maxLength(4),
                        TimePicker::make('not_before')
                            ->label(__('Do not allocate before (UTC)'))
                            ->helperText('Will not allocate this stand automatically for arrivals before this time')
                    ]),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }    
}
