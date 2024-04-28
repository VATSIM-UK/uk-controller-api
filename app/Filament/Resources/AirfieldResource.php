<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasCoordinates;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\AirfieldResource\Pages;
use App\Filament\Resources\AirfieldResource\RelationManagers\ControllersRelationManager;
use App\Models\Airfield\Airfield;
use App\Rules\Airfield\AirfieldIcao;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class AirfieldResource extends Resource
{
    use TranslatesStrings;
    use HasCoordinates;

    protected static ?string $model = Airfield::class;
    protected static ?string $navigationIcon = 'heroicon-o-x-circle';
    protected static ?string $recordTitleAttribute = 'code';
    protected static ?string $navigationGroup = 'Airfield';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Fieldset::make('identifiers')
                    ->label(self::translateFormPath('fieldset_identifiers.label'))
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->rule(new AirfieldIcao())
                            ->required()
                            ->label(self::translateFormPath('code.label'))
                            ->helperText(self::translateFormPath('code.helper'))
                            ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord),
                        ...self::coordinateInputs(),
                        TextInput::make('elevation')
                            ->required()
                            ->label(self::translateFormPath('elevation.label'))
                            ->helperText(self::translateFormPath('elevation.helper'))
                            ->integer(),
                        Select::make('wake_category_scheme_id')
                            ->required()
                            ->label(self::translateFormPath('wake_scheme.label'))
                            ->helperText(self::translateFormPath('wake_scheme.helper'))
                            ->options(SelectOptions::wakeSchemes()),
                    ]),
                Fieldset::make('altimetry')
                    ->label(self::translateFormPath('fieldset_altimetry.label'))
                    ->schema([
                        TextInput::make('transition_altitude')
                            ->required()
                            ->label(self::translateFormPath('transition_altitude.label'))
                            ->helperText(self::translateFormPath('transition_altitude.helper'))
                            ->integer()
                            ->minValue(0)
                            ->maxValue(20000),
                        Toggle::make('standard_high')
                            ->label(self::translateFormPath('standard_high.label'))
                            ->helperText(self::translateFormPath('standard_high.helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('transition_altitude')
                    ->label(self::translateTablePath('columns.transition')),
                Tables\Columns\TextColumn::make('runways.identifier')
                    ->label(self::translateTablePath('columns.runways')),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.top_down'))
                    ->default(['--']),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->defaultSort('code');
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
            'index' => Pages\ListAirfields::route('/'),
            'create' => Pages\CreateAirfield::route('/create'),
            'view' => Pages\ViewAirfield::route('/{record}'),
            'edit' => Pages\EditAirfield::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'airfields';
    }
}
