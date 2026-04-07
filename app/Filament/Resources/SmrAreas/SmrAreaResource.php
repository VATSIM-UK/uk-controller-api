<?php

namespace App\Filament\Resources\SmrAreas;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\TernaryFilter;
use App\Filament\Resources\SmrAreas\Pages\ListSmrAreas;
use App\Filament\Resources\SmrAreas\Pages\CreateSmrArea;
use App\Filament\Resources\SmrAreas\Pages\ViewSmrArea;
use App\Filament\Resources\SmrAreas\Pages\EditSmrArea;
use App\Filament\Helpers\HasCoordinates;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\SmrAreaResource\Pages;
use App\Models\SmrArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\App;

class SmrAreaResource extends Resource
{
    use TranslatesStrings;
    use HasCoordinates;

    protected static ?string $model = SmrArea::class;
    protected static ?string $modelLabel = 'SMR Area';
    protected static string | \UnitEnum | null $navigationGroup = 'Airfield';
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'SMR Areas';
    protected static ?string $pluralModelLabel = 'SMR Areas';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('airfield_id')
                    ->label(self::translateFormPath('airfield.label'))
                    ->helperText(self::translateFormPath('airfield.helper'))
                    ->options(SelectOptions::airfields())
                    ->searchable(!App::runningUnitTests())
                    ->required(),
                TextInput::make('name')
                    ->label(self::translateFormPath('name.label'))
                    ->helperText(self::translateFormPath('name.helper'))
                    ->minLength(1)
                    ->maxLength(255),
                TextInput::make('source')
                    ->label(self::translateFormPath('source.label'))
                    ->helperText(self::translateFormPath('source.helper'))
                    ->minLength(1)
                    ->maxLength(255),
                Textarea::make('coordinates')
                    ->label(self::translateFormPath('coordinates.label'))
                    ->helperText(self::translateFormPath('coordinates.helper'))
                    ->rows(5)
                    ->required()
                    // relatively lax validation; a stricter pattern is somewhat unnecessary
                    ->regex('/^(COORD(:[NESW][\d\.]{13}){2}\n*){3,}$/'),
                DateTimePicker::make('start_date')
                    ->label(self::translateFormPath('start_date.label'))
                    ->helperText(self::translateFormPath('start_date.helper')),
                DateTimePicker::make('end_date')
                    ->label(self::translateFormPath('end_date.label'))
                    ->helperText(self::translateFormPath('end_date.helper'))
                    ->after('start_date'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('name')
                    ->label(self::translateTablePath('columns.name'))
                    ->searchable(),
                TextColumn::make('source')
                    ->label(self::translateTablePath('columns.source'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->filters([
                Filter::make('expired')
                    ->toggle()
                    ->label(self::translateFilterPath('expired'))
                    ->query(fn (Builder $query) => $query->expired()),
                TernaryFilter::make('activation')
                    ->label(self::translateFilterPath('activation.label'))
                    ->trueLabel(self::translateFilterPath('activation.true'))
                    ->falseLabel(self::translateFilterPath('activation.false'))
                    ->queries(
                        true:  fn (Builder $query) => $query->active(),
                        false: fn (Builder $query) => $query->whereNot->active(),
                        blank: fn (Builder $query) => $query,
                    ),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSmrAreas::route('/'),
            'create' => CreateSmrArea::route('/create'),
            'view' => ViewSmrArea::route('/{record}'),
            'edit' => EditSmrArea::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'smr_areas';
    }
}
