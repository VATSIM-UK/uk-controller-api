<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ControllerPositionResource\Pages;
use App\Models\Controller\ControllerPosition;
use App\Rules\Controller\ControllerPositionCallsign;
use App\Rules\Controller\ControllerPositionFrequency;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class ControllerPositionResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = ControllerPosition::class;

    protected static ?string $navigationIcon = 'heroicon-o-microphone';
    protected static ?string $recordTitleAttribute = 'callsign';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('callsign')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->label(__('form.controllers.callsign.label'))
                    ->helperText(__('form.controllers.callsign.helper'))
                    ->rule(new ControllerPositionCallsign()),
                Forms\Components\TextInput::make('frequency')
                    ->required()
                    ->rule(new ControllerPositionFrequency())
                    ->label(__('form.controllers.frequency.label'))
                    ->helperText(__('form.controllers.frequency.helper'))
                    ->length(7),
                Forms\Components\Toggle::make('requests_departure_releases')
                    ->label(__('form.controllers.requests_departure_releases.label'))
                    ->helperText(__('form.controllers.requests_departure_releases.helper')),
                Forms\Components\Toggle::make('receives_departure_releases')
                    ->label(__('form.controllers.receives_departure_releases.label'))
                    ->helperText(__('form.controllers.receives_departure_releases.helper')),
                Forms\Components\Toggle::make('sends_prenotes')
                    ->label(__('form.controllers.sends_prenotes.label'))
                    ->helperText(__('form.controllers.sends_prenotes.helper')),
                Forms\Components\Toggle::make('receives_prenotes')
                    ->label(__('form.controllers.receives_prenotes.label'))
                    ->helperText(__('form.controllers.receives_prenotes.helper')),
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
