<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrenoteResource\Pages;
use App\Filament\Resources\PrenoteResource\RelationManagers;
use App\Models\Controller\Prenote;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class PrenoteResource extends Resource
{
    protected static ?string $model = Prenote::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label(__('form.prenotes.description.label'))
                    ->maxLength(255)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('table.prenotes.columns.description'))
                    ->searchable(),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(__('table.prenotes.columns.controllers'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ControllersRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPrenotes::route('/'),
            'create' => Pages\CreatePrenote::route('/create'),
            'view' => Pages\ViewPrenote::route('/{record}'),
            'edit' => Pages\EditPrenote::route('/{record}/edit'),
        ];
    }
}
