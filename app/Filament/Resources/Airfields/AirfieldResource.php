<?php

namespace App\Filament\Resources\Airfields;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\Airfields\Pages\ListAirfields;
use App\Filament\Resources\Airfields\Pages\CreateAirfield;
use App\Filament\Resources\Airfields\Pages\ViewAirfield;
use App\Filament\Resources\Airfields\Pages\EditAirfield;
use App\Filament\Helpers\HasCoordinates;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\AirfieldResource\Pages;
use App\Filament\Resources\Airfields\RelationManagers\ControllersRelationManager;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Airfield\Airfield;
use App\Rules\Airfield\AirfieldIcao;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Pages\Page;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;

class AirfieldResource extends Resource
{
    use TranslatesStrings;
    use HasCoordinates;

    protected static ?string $model = Airfield::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-x-circle';
    protected static ?string $recordTitleAttribute = 'code';
    protected static string | \UnitEnum | null $navigationGroup = 'Airfield';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Fieldset::make('identifiers')
                    ->label(self::translateFormPath('fieldset_identifiers.label'))
                    ->schema([
                        TextInput::make('code')
                            ->unique(ignoreRecord: true)
                            ->rule(new AirfieldIcao())
                            ->required()
                            ->label(self::translateFormPath('code.label'))
                            ->helperText(self::translateFormPath('code.helper'))
                            ->disabled(fn(Page $livewire) => !$livewire instanceof CreateRecord),
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
                TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('transition_altitude')
                    ->label(self::translateTablePath('columns.transition')),
                TextColumn::make('runways.identifier')
                    ->label(self::translateTablePath('columns.runways')),
                TagsColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.top_down'))
                    ->default(['--']),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
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
            'index' => ListAirfields::route('/'),
            'create' => CreateAirfield::route('/create'),
            'view' => ViewAirfield::route('/{record}'),
            'edit' => EditAirfield::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'airfields';
    }
}
