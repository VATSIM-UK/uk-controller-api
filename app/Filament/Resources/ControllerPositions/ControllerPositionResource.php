<?php

namespace App\Filament\Resources\ControllerPositions;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\ControllerPositions\Pages\ListControllerPositions;
use App\Filament\Resources\ControllerPositions\Pages\CreateControllerPosition;
use App\Filament\Resources\ControllerPositions\Pages\ViewControllerPosition;
use App\Filament\Resources\ControllerPositions\Pages\EditControllerPosition;
use App\Filament\Resources\ControllerPositionResource\Pages;
use App\Models\Controller\ControllerPosition;
use App\Rules\Controller\ControllerPositionCallsign;
use App\Rules\Controller\ControllerPositionFrequency;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ControllerPositionResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = ControllerPosition::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $recordTitleAttribute = 'callsign';
    protected static string | \UnitEnum | null $navigationGroup = 'Controller';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('identifiers')
                    ->label(self::translateFormPath('identifiers_section.label'))
                    ->schema([
                        TextInput::make('callsign')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label(self::translateFormPath('callsign.label'))
                            ->helperText(self::translateFormPath('callsign.helper'))
                            ->rule(new ControllerPositionCallsign()),
                        TextInput::make('description')
                            ->maxLength(255)
                            ->label(self::translateFormPath('description.label'))
                            ->helperText(self::translateFormPath('description.helper')),
                        TextInput::make('frequency')
                            ->required()
                            ->rule(new ControllerPositionFrequency())
                            ->label(self::translateFormPath('frequency.label'))
                            ->helperText(self::translateFormPath('frequency.helper'))
                            ->length(7),
                    ]),
                    Fieldset::make('coordination')
                        ->label(self::translateFormPath('coordination_section.label'))
                        ->schema([
                            Toggle::make('requests_departure_releases')
                                ->label(self::translateFormPath('requests_departure_releases.label'))
                                ->helperText(self::translateFormPath('requests_departure_releases.helper')),
                            Toggle::make('receives_departure_releases')
                                ->label(self::translateFormPath('receives_departure_releases.label'))
                                ->helperText(self::translateFormPath('receives_departure_releases.helper')),
                            Toggle::make('sends_prenotes')
                                ->label(self::translateFormPath('sends_prenotes.label'))
                                ->helperText(self::translateFormPath('sends_prenotes.helper')),
                            Toggle::make('receives_prenotes')
                                ->label(self::translateFormPath('receives_prenotes.label'))
                                ->helperText(self::translateFormPath('receives_prenotes.helper')),
                        ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign'))
                    ->sortable()
                    ->searchable(),
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description'))
                    ->formatStateUsing(fn (?string $state) => $state ?: '--')
                    ->searchable(),
                TextColumn::make('frequency')
                    ->label(self::translateTablePath('columns.frequency'))
                    ->searchable(),
                TagsColumn::make('topDownAirfields.code')
                    ->label(self::translateTablePath('columns.top_down')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->defaultSort('callsign', 'asc');
    }
    
    public static function getPages(): array
    {
        return [
            'index' => ListControllerPositions::route('/'),
            'create' => CreateControllerPosition::route('/create'),
            'view' => ViewControllerPosition::route('/{record}'),
            'edit' => EditControllerPosition::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'controllers';
    }
}
