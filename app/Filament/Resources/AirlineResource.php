<?php

namespace App\Filament\Resources;

use App\Events\Airline\AirlinesUpdatedEvent;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\AirlineResource\Pages;
use App\Filament\Resources\AirlineResource\RelationManagers\StandsRelationManager;
use App\Filament\Resources\AirlineResource\RelationManagers\TerminalsRelationManager;
use App\Models\Airline\Airline;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
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
                Fieldset::make(self::translateFormPath('fieldset_creation_options.label'))
                    ->schema(
                        [
                            Select::make('copy_stand_assignments')
                                ->options(SelectOptions::airlines())
                                ->searchable()
                                ->label(self::translateFormPath('copy_stand_assignments.label'))
                                ->helperText(self::translateFormPath('copy_stand_assignments.helper'))
                        ]
                    )
                    ->hidden(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord),
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
                DeleteAction::make()
                    ->after(function () {
                        event(new AirlinesUpdatedEvent);
                    }),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            TerminalsRelationManager::class,
            StandsRelationManager::class,
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
