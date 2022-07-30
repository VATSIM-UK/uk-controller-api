<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HandoffResource\Pages;
use App\Filament\Resources\HandoffResource\RelationManagers\ControllersRelationManager;
use App\Models\Controller\Handoff;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class HandoffResource extends Resource
{
    protected static ?string $model = Handoff::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $recordTitleAttribute = 'description';

    public static function getEloquentQuery(): Builder
    {
        return Handoff::with('controllers');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label(__('form.handoffs.description.label'))
                    ->maxLength(255)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('table.handoffs.columns.description'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(__('table.handoffs.columns.controllers'))
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            ControllersRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListHandoffs::route('/'),
            'create' => Pages\CreateHandoff::route('/create'),
            'view' => Pages\ViewHandoff::route('/{record}'),
            'edit' => Pages\EditHandoff::route('/{record}/edit'),
        ];
    }    
}
