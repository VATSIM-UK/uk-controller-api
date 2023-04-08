<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AirlineResource\Pages;
use App\Models\Airline\Airline;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class AirlineResource extends Resource
{
    use TranslatesStrings;

    public static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $recordTitleAttribute = 'icao_code';
    protected static ?string $navigationGroup = 'Airline';
    protected static ?string $model = Airline::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('icao_code')
                    ->required()
                    ->maxLength(255)
                    ->label(self::translateFormPath('icao_code.label')),
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->label(self::translateFormPath('name.label')),
                TextInput::make('callsign')
                    ->required()
                    ->maxLength(255)
                    ->label(self::translateFormPath('callsign.label'))
                    ->helperText(self::translateFormPath('callsign.helper')),
                Toggle::make('is_cargo')
                    ->label(self::translateFormPath('is_cargo.label'))
                    ->helperText(self::translateFormPath('is_cargo.helper'))
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('icao_code')
                    ->label(self::translateTablePath('columns.icao_code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(self::translateTablePath('columns.name')),
                TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign')),
                IconColumn::make('is_cargo')
                    ->label(self::translateTablePath('columns.is_cargo'))
                    ->boolean(),
            ])
            ->actions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
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
            'index' => Pages\ListAirlines::route('/'),
            'create' => Pages\CreateAirline::route('/create'),
            'view' => Pages\ViewAirline::route('/{record}'),
            'edit' => Pages\EditAirline::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines';
    }
}
