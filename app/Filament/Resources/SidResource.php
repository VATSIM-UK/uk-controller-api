<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\SidResource\Pages;
use App\Filament\Resources\SidResource\RelationManagers;
use App\Models\Runway\Runway;
use App\Models\Sid;
use App\Rules\Sid\SidIdentifiersMustBeUniqueForRunway;
use Closure;
use Filament\Forms;
use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class SidResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Sid::class;
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $recordRouteKeyName = 'sid.id';
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationLabel = 'SIDs';
    protected static ?string $navigationGroup = 'Airfield';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Select::make('runway_id')
                    ->label(self::translateFormPath('runway.label'))
                    ->helperText(self::translateFormPath('runway.helper'))
                    ->hintIcon('heroicon-o-chevron-double-up')
                    ->options(SelectOptions::runways())
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->searchable()
                    ->required(),
                Forms\Components\TextInput::make('identifier')
                    ->label(self::translateFormPath('identifier.label'))
                    ->helperText(self::translateFormPath('identifier.helper'))
                    ->rule(
                        fn (\Filament\Forms\Get $get, ?Model $record) => new SidIdentifiersMustBeUniqueForRunway(
                            Runway::findOrFail($get('runway_id')),
                            $record
                        ),
                        fn (\Filament\Forms\Get $get) => $get('runway_id')
                    )
                    ->required(),
                Forms\Components\TextInput::make('initial_altitude')
                    ->label(self::translateFormPath('initial_altitude.label'))
                    ->helperText(self::translateFormPath('initial_altitude.helper'))
                    ->hintIcon('heroicon-o-presentation-chart-line')
                    ->integer()
                    ->minValue(0)
                    ->maxValue(99999)
                    ->required(),
                Forms\Components\TextInput::make('initial_heading')
                    ->label(self::translateFormPath('initial_heading.label'))
                    ->helperText(self::translateFormPath('initial_heading.helper'))
                    ->hintIcon('heroicon-o-arrows-pointing-out')
                    ->integer()
                    ->minValue(1)
                    ->maxValue(360),
                Select::make('handoff_id')
                    ->label(self::translateFormPath('handoff.label'))
                    ->helperText(self::translateFormPath('handoff.helper'))
                    ->hintIcon('heroicon-o-clipboard-document-list')
                    ->options(SelectOptions::nonAirfieldHandoffs())
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('runway.airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('runway.identifier')
                    ->label(self::translateTablePath('columns.runway')),
                Tables\Columns\TextColumn::make('identifier')
                    ->label(self::translateTablePath('columns.identifier')),
                Tables\Columns\TextColumn::make('initial_altitude')
                    ->label(self::translateTablePath('columns.initial_altitude')),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
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
                                'runway.airfield',
                                function (Builder $airfield) use ($data) {
                                    return $airfield->where('id', $data['value']);
                                }
                            );
                        }
                    )
                
            ])
            ->defaultSort('runway.airfield.code', 'asc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PrenotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSids::route('/'),
            'create' => Pages\CreateSid::route('/create'),
            'view' => Pages\ViewSid::route('/{record}'),
            'edit' => Pages\EditSid::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'sids';
    }
}
