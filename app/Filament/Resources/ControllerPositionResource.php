<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControllerPositionResource\Pages;
use App\Filament\Resources\ControllerPositionResource\RelationManagers;
use App\Models\Controller\ControllerPosition;
use App\Rules\Controller\ControllerPositionCallsign;
use App\Rules\Controller\ControllerPositionFrequency;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ControllerPositionResource extends Resource
{
    protected static ?string $model = ControllerPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('callsign')
                    ->required()
                    ->unique(null, null, null, null, true)
                    ->helperText('The callsign, e.g. EGLL_S_TWR')
                    ->rule(new ControllerPositionCallsign()),
                Forms\Components\TextInput::make('frequency')
                    ->numeric(false)
                    ->required()
                    ->rule(new ControllerPositionFrequency())
                    ->label('Frequency')
                    ->helperText('The full, 6 digit frequency')
                    ->length(7),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('id'),
                Tables\Columns\TextColumn::make('callsign')
                    ->label('Callsign')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label('Frequency')
                    ->searchable(),
            ])
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
            'index' => Pages\ListControllerPositions::route('/'),
            'create' => Pages\CreateControllerPosition::route('/create'),
            'edit' => Pages\EditControllerPosition::route('/{record}/edit'),
        ];
    }    
}
