<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PrenoteResource\Pages;
use App\Filament\Resources\PrenoteResource\RelationManagers;
use App\Models\Controller\Prenote;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class PrenoteResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Prenote::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $recordTitleAttribute = 'description';
    protected static ?string $navigationGroup = 'Airfield';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.controllers'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])->defaultSort('description');
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

    protected static function translationPathRoot(): string
    {
        return 'prenotes';
    }
}
