<?php

namespace App\Filament\Resources\Sids;

use Filament\Schemas\Schema;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Tables\Filters\SelectFilter;
use App\Filament\Resources\Sids\RelationManagers\PrenotesRelationManager;
use App\Filament\Resources\Sids\Pages\ListSids;
use App\Filament\Resources\Sids\Pages\CreateSid;
use App\Filament\Resources\Sids\Pages\ViewSid;
use App\Filament\Resources\Sids\Pages\EditSid;
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
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-map';
    protected static ?string $recordRouteKeyName = 'sid.id';
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationLabel = 'SIDs';
    protected static string | \UnitEnum | null $navigationGroup = 'Airfield';

    public static function getPluralModelLabel(): string
    {
        return 'SIDs';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('runway_id')
                    ->label(self::translateFormPath('runway.label'))
                    ->helperText(self::translateFormPath('runway.helper'))
                    ->hintIcon('heroicon-o-chevron-double-up')
                    ->options(SelectOptions::runways())
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->searchable()
                    ->required(),
                TextInput::make('identifier')
                    ->label(self::translateFormPath('identifier.label'))
                    ->helperText(self::translateFormPath('identifier.helper'))
                    ->rule(
                        fn (Get $get, ?Model $record) => new SidIdentifiersMustBeUniqueForRunway(
                            Runway::findOrFail($get('runway_id')),
                            $record
                        ),
                        fn (Get $get) => $get('runway_id')
                    )
                    ->required(),
                TextInput::make('initial_altitude')
                    ->label(self::translateFormPath('initial_altitude.label'))
                    ->helperText(self::translateFormPath('initial_altitude.helper'))
                    ->hintIcon('heroicon-o-presentation-chart-line')
                    ->integer()
                    ->minValue(0)
                    ->maxValue(99999)
                    ->required(),
                TextInput::make('initial_heading')
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
                TextColumn::make('runway.airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('runway.identifier')
                    ->label(self::translateTablePath('columns.runway')),
                TextColumn::make('identifier')
                    ->label(self::translateTablePath('columns.identifier')),
                TextColumn::make('initial_altitude')
                    ->label(self::translateTablePath('columns.initial_altitude')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->filters([
                SelectFilter::make('airfield')
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
            PrenotesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSids::route('/'),
            'create' => CreateSid::route('/create'),
            'view' => ViewSid::route('/{record}'),
            'edit' => EditSid::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'sids';
    }
}
