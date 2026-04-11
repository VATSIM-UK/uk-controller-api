<?php

namespace App\Filament\Resources\Aircraft;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use App\Filament\Resources\Aircraft\Pages\ListAircraft;
use App\Filament\Resources\Aircraft\Pages\CreateAircraft;
use App\Filament\Resources\Aircraft\Pages\ViewAircraft;
use App\Filament\Resources\Aircraft\Pages\EditAircraft;
use App\Events\Aircraft\AircraftDataUpdatedEvent;
use App\Filament\Resources\Aircraft\RelationManagers\WakeCategoriesRelationManager;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class AircraftResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Aircraft::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-paper-airplane';
    protected static string|\UnitEnum|null $navigationGroup = 'Airline';
    protected static ?string $recordTitleAttribute = 'code';

    public static function getEloquentQuery(): Builder
    {
        return Aircraft::with('wakeCategories', 'wakeCategories.scheme');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('code')
                    ->label(self::translateFormPath('code.label'))
                    ->helperText(self::translateFormPath('code.helper'))
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->autofocus(),
                Select::make('aerodrome_reference_code')
                    ->label(self::translateFormPath('aerodrome_reference_code.label'))
                    ->helperText(self::translateFormPath('aerodrome_reference_code.helper'))
                    ->options([
                        'A' => 'A',
                        'B' => 'B',
                        'C' => 'C',
                        'D' => 'D',
                        'E' => 'E',
                        'F' => 'F',
                        'G' => 'G',
                    ])
                    ->required(),
                TextInput::make('wingspan')
                    ->label(self::translateFormPath('wingspan.label'))
                    ->required()
                    ->numeric()
                    ->minValue(0),
                TextInput::make('length')
                    ->label(self::translateFormPath('length.label'))
                    ->required()
                    ->numeric()
                    ->minValue(0),
                Toggle::make('allocate_stands')
                    ->required()
                    ->label(self::translateFormPath('allocate_stands.label'))
                    ->helperText(self::translateFormPath('allocate_stands.helper'))
                    ->default(false),
                Toggle::make('is_business_aviation')
                    ->label(self::translateFormPath('is_business_aviation.label'))
                    ->helperText(self::translateFormPath('is_business_aviation.helper'))
                    ->default(false)
                    ->required(),
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
                TextColumn::make('aerodrome_reference_code')
                    ->label(self::translateTablePath('columns.aerodrome_reference_code'))
                    ->sortable(),
                TextColumn::make('wingspan')
                    ->label(self::translateTablePath('columns.wingspan')),
                TextColumn::make('length')
                    ->label(self::translateTablePath('columns.length')),
                TextColumn::make('wakeCategories')
                    ->label(self::translateTablePath('columns.wake_categories'))
                    ->badge()
                    ->getStateUsing(
                        fn (Aircraft $record) => $record->wakeCategories->map(
                            fn (WakeCategory $category) => sprintf(
                                '%s: %s',
                                $category->scheme->name,
                                $category->description
                            )
                        )->toArray()
                    ),
                IconColumn::make('allocate_stands')
                    ->label(self::translateTablePath('columns.allocate_stands'))
                    ->boolean(),
                IconColumn::make('is_business_aviation')
                    ->label(self::translateTablePath('columns.is_business_aviation'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make()
                    ->after(function () {
                        event(new AircraftDataUpdatedEvent);
                    }),
            ])
            ->defaultSort('code');
    }

    public static function getRelations(): array
    {
        return [
            WakeCategoriesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAircraft::route('/'),
            'create' => CreateAircraft::route('/create'),
            'view' => ViewAircraft::route('/{record}'),
            'edit' => EditAircraft::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'aircraft';
    }
}
