<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControllerPositionResource\Pages;
use App\Models\Controller\ControllerPosition;
use App\Rules\Controller\ControllerPositionCallsign;
use App\Rules\Controller\ControllerPositionFrequency;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class ControllerPositionResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = ControllerPosition::class;
    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $recordTitleAttribute = 'callsign';
    protected static ?string $navigationGroup = 'Controller';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Fieldset::make('identifiers')
                    ->label(self::translateFormPath('identifiers_section.label'))
                    ->schema([
                        Forms\Components\TextInput::make('callsign')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->label(self::translateFormPath('callsign.label'))
                            ->helperText(self::translateFormPath('callsign.helper'))
                            ->rule(new ControllerPositionCallsign()),
                        Forms\Components\TextInput::make('description')
                            ->maxLength(255)
                            ->label(self::translateFormPath('description.label'))
                            ->helperText(self::translateFormPath('description.helper')),
                        Forms\Components\TextInput::make('frequency')
                            ->required()
                            ->rule(new ControllerPositionFrequency())
                            ->label(self::translateFormPath('frequency.label'))
                            ->helperText(self::translateFormPath('frequency.helper'))
                            ->length(7),
                    ]),
                    Forms\Components\Fieldset::make('coordination')
                        ->label(self::translateFormPath('coordination_section.label'))
                        ->schema([
                            Forms\Components\Toggle::make('requests_departure_releases')
                                ->label(self::translateFormPath('requests_departure_releases.label'))
                                ->helperText(self::translateFormPath('requests_departure_releases.helper')),
                            Forms\Components\Toggle::make('receives_departure_releases')
                                ->label(self::translateFormPath('receives_departure_releases.label'))
                                ->helperText(self::translateFormPath('receives_departure_releases.helper')),
                            Forms\Components\Toggle::make('sends_prenotes')
                                ->label(self::translateFormPath('sends_prenotes.label'))
                                ->helperText(self::translateFormPath('sends_prenotes.helper')),
                            Forms\Components\Toggle::make('receives_prenotes')
                                ->label(self::translateFormPath('receives_prenotes.label'))
                                ->helperText(self::translateFormPath('receives_prenotes.helper')),
                        ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description'))
                    ->formatStateUsing(fn (?string $state) => $state ?: '--')
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(self::translateTablePath('columns.frequency'))
                    ->searchable(),
                Tables\Columns\TagsColumn::make('topDownAirfields.code')
                    ->label(self::translateTablePath('columns.top_down')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListControllerPositions::route('/'),
            'create' => Pages\CreateControllerPosition::route('/create'),
            'view' => Pages\ViewControllerPosition::route('/{record}'),
            'edit' => Pages\EditControllerPosition::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'controllers';
    }
}
