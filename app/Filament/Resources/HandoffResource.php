<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HandoffResource\Pages;
use App\Filament\Resources\HandoffResource\RelationManagers\ControllersRelationManager;
use App\Models\Controller\Handoff;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class HandoffResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Handoff::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'description';
    protected static ?string $navigationGroup = 'Airfield';

    public static function getEloquentQuery(): Builder
    {
        return Handoff::with('controllers')
            ->whereDoesntHave('airfield');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('description')
                    ->label(self::translateFormPath('description.label'))
                    ->maxLength(255)
                    ->required()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TagsColumn::make('sids.identifier')
                    ->label(self::translateTablePath('columns.sids')),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.controllers')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ])->defaultSort('description');
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

    protected static function translationPathRoot(): string
    {
        return 'handoffs';
    }
}
