<?php

namespace App\Filament\Resources\Prenotes;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\Prenotes\RelationManagers\ControllersRelationManager;
use App\Filament\Resources\Prenotes\Pages\ListPrenotes;
use App\Filament\Resources\Prenotes\Pages\CreatePrenote;
use App\Filament\Resources\Prenotes\Pages\ViewPrenote;
use App\Filament\Resources\Prenotes\Pages\EditPrenote;
use App\Filament\Resources\PrenoteResource\Pages;
use App\Filament\Resources\PrenoteResource\RelationManagers;
use App\Models\Controller\Prenote;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use App\Filament\Resources\TranslatesStrings;

class PrenoteResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Prenote::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'description';
    protected static string | \UnitEnum | null $navigationGroup = 'Airfield';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('description')
                    ->label(self::translateFormPath('description.label'))
                    ->maxLength(255)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description'))
                    ->sortable()
                    ->searchable(),
                TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.controllers'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
            'index' => ListPrenotes::route('/'),
            'create' => CreatePrenote::route('/create'),
            'view' => ViewPrenote::route('/{record}'),
            'edit' => EditPrenote::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'prenotes';
    }
}
