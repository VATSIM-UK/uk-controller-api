<?php

namespace App\Filament\Resources\Airlines;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Airlines\Pages\ListAirlines;
use App\Filament\Resources\Airlines\Pages\CreateAirline;
use App\Filament\Resources\Airlines\Pages\ViewAirline;
use App\Filament\Resources\Airlines\Pages\EditAirline;
use App\Events\Airline\AirlinesUpdatedEvent;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\AirlineResource\Pages;
use App\Filament\Resources\Airlines\RelationManagers\StandsRelationManager;
use App\Filament\Resources\Airlines\RelationManagers\TerminalsRelationManager;
use App\Models\Airline\Airline;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use App\Filament\Resources\TranslatesStrings;

class AirlineResource extends Resource
{
    use TranslatesStrings;

    public static string | \BackedEnum | null $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $recordTitleAttribute = 'icao_code';
    protected static string | \UnitEnum | null $navigationGroup = 'Airline';
    protected static ?string $model = Airline::class;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
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
                    ->hidden(fn(Page $livewire) => !$livewire instanceof CreateRecord)
                    ->disabled(fn(Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn(Page $livewire) => $livewire instanceof CreateRecord),
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
            ->recordActions([
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
            'index' => ListAirlines::route('/'),
            'create' => CreateAirline::route('/create'),
            'view' => ViewAirline::route('/{record}'),
            'edit' => EditAirline::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines';
    }
}
