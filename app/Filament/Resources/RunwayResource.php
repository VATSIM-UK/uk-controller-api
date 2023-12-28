<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasCoordinates;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\RunwayResource\Pages;
use App\Models\Runway\Runway;
use App\Rules\Heading\ValidHeading;
use App\Rules\Runway\RunwayIdentifier;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class RunwayResource extends Resource
{
    use TranslatesStrings;
    use HasCoordinates;

    protected static ?string $model = Runway::class;
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationIcon = 'heroicon-o-arrow-up';
    protected static ?string $navigationGroup = 'Airfield';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('airfield_id')
                    ->label(self::translateFormPath('airfield.label'))
                    ->helperText(self::translateFormPath('airfield.helper'))
                    ->searchable()
                    ->options(SelectOptions::airfields())
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->required(),
                TextInput::make('identifier')
                    ->label(self::translateFormPath('identifier.label'))
                    ->helperText(self::translateFormPath('identifier.helper'))
                    ->required()
                    ->rule(new RunwayIdentifier()),
                ...self::coordinateInputs('threshold_latitude', 'threshold_longitude'),
                TextInput::make('threshold_elevation')
                    ->label(self::translateFormPath('threshold_elevation.label'))
                    ->helperText(self::translateFormPath('threshold_elevation.helper'))
                    ->numeric()
                    ->integer()
                    ->required(),
                TextInput::make('heading')
                    ->label(self::translateFormPath('heading.label'))
                    ->helperText(self::translateFormPath('heading.helper'))
                    ->required()
                    ->rule(new ValidHeading()),
                TextInput::make('glideslope_angle')
                    ->label(self::translateFormPath('glideslope_angle.label'))
                    ->helperText(self::translateFormPath('glideslope_angle.helper'))
                    ->numeric()
                    ->minValue(1)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(self::translateTablePath('columns.identifier'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('heading')
                    ->label(self::translateTablePath('columns.heading')),
                Tables\Columns\TagsColumn::make('sids.identifier')
                    ->label(self::translateTablePath('columns.sids')),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('airfield')
                    ->label(self::translateFilterPath('airfield'))
                    ->options(SelectOptions::airfields())
                    ->searchable()
                    ->query(
                        function (Builder $query, array $data) {
                            if (empty($data['value'])) {
                                return $query;
                            }

                            return $query->whereHas(
                                'airfield',
                                function (Builder $airfield) use ($data) {
                                    return $airfield->where('id', $data['value']);
                                }
                            );
                        }
                    ),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make()
            ]);
    }
    
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRunways::route('/'),
            'create' => Pages\CreateRunway::route('/create'),
            'view' => Pages\ViewRunway::route('/{record}'),
            'edit' => Pages\EditRunway::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'runways';
    }
}
