<?php

namespace App\Filament\Resources\Handoffs;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\Handoffs\Pages\ListHandoffs;
use App\Filament\Resources\Handoffs\Pages\CreateHandoff;
use App\Filament\Resources\Handoffs\Pages\ViewHandoff;
use App\Filament\Resources\Handoffs\Pages\EditHandoff;
use App\Filament\Resources\HandoffResource\Pages;
use App\Filament\Resources\Handoffs\RelationManagers\ControllersRelationManager;
use App\Models\Controller\Handoff;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class HandoffResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Handoff::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'description';
    protected static string | \UnitEnum | null $navigationGroup = 'Airfield';

    public static function getEloquentQuery(): Builder
    {
        return Handoff::with('controllers')
            ->whereDoesntHave('airfield');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description'))
                    ->searchable()
                    ->sortable(),
                TagsColumn::make('sids.identifier')
                    ->label(self::translateTablePath('columns.sids')),
                TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.controllers')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make()
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
            'index' => ListHandoffs::route('/'),
            'create' => CreateHandoff::route('/create'),
            'view' => ViewHandoff::route('/{record}'),
            'edit' => EditHandoff::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'handoffs';
    }
}
