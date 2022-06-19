<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StandResource\Pages;
use App\Filament\Resources\StandResource\RelationManagers;
use App\Models\Airline\Airline;
use App\Models\Stand\Stand;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Collection;

class StandResource extends Resource
{
    protected static ?string $model = Stand::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('Id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(__('Airfield'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('terminal.description')
                    ->label(__('Terminal'))
                    ->default('--')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(__('Identifier'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TagsColumn::make('uniqueAirlines.icao_code')
                    ->label(__('Airlines'))
                    ->default(['--'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('assignment_priority')
                    ->label(__('Assignment priority (lower is higher)'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\BooleanColumn::make('isOpen')
                    ->label(__('Available for Allocation'))
            ])->defaultSort('airfield.code')
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DeleteBulkAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            //
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListStands::route('/'),
            'create' => Pages\CreateStand::route('/create'),
            'edit' => Pages\EditStand::route('/{record}/edit'),
        ];
    }    
}
