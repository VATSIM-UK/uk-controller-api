<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AircraftResource\Pages;
use App\Filament\Resources\AircraftResource\RelationManagers\WakeCategoriesRelationManager;
use App\Models\Aircraft\Aircraft;
use App\Models\Aircraft\WakeCategory;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class AircraftResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Aircraft::class;

    protected static ?string $navigationIcon = 'heroicon-o-paper-airplane';
    protected static ?string $navigationGroup = 'Airline';
    protected static ?string $recordTitleAttribute = 'code';

    public static function getEloquentQuery(): Builder
    {
        return Aircraft::with('wakeCategories', 'wakeCategories.scheme');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('aerodrome_reference_code')
                    ->label(self::translateTablePath('columns.aerodrome_reference_code'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('wingspan')
                    ->label(self::translateTablePath('columns.wingspan')),
                Tables\Columns\TextColumn::make('length')
                    ->label(self::translateTablePath('columns.length')),
                Tables\Columns\TagsColumn::make('wakeCategories')
                    ->label(self::translateTablePath('columns.wake_categories'))
                    ->getStateUsing(
                        fn (Aircraft $record) => $record->wakeCategories->map(
                            fn (WakeCategory $category) => sprintf(
                                '%s: %s',
                                $category->scheme->name,
                                $category->description
                            )
                        )->toArray()
                    ),
                Tables\Columns\IconColumn::make('allocate_stands')
                    ->label(self::translateTablePath('columns.allocate_stands'))
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListAircraft::route('/'),
            'create' => Pages\CreateAircraft::route('/create'),
            'view' => Pages\ViewAircraft::route('/{record}'),
            'edit' => Pages\EditAircraft::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'aircraft';
    }
}
