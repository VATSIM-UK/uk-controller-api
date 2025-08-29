<?php

namespace App\Filament\Resources;

use App\Filament\Helpers\HasCoordinates;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\SmrAreaResource\Pages;
use App\Models\SmrArea;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Form;
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
    protected static ?string $navigationGroup = 'Airfield';
    protected static ?string $navigationIcon = 'heroicon-o-exclamation-triangle';
    protected static ?string $navigationLabel = 'SMR Areas';
    protected static ?string $pluralModelLabel = 'SMR Areas';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
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
                Tables\Columns\TextColumn::make('airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(self::translateTablePath('columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('source')
                    ->label(self::translateTablePath('columns.source'))
                    ->searchable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->filters([
                Tables\Filters\Filter::make('expired')
                    ->toggle()
                    ->label(self::translateFilterPath('expired'))
                    ->query(fn (Builder $query) => $query->expired()),
                Tables\Filters\TernaryFilter::make('activation')
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
            'index' => Pages\ListSmrAreas::route('/'),
            'create' => Pages\CreateSmrArea::route('/create'),
            'view' => Pages\ViewSmrArea::route('/{record}'),
            'edit' => Pages\EditSmrArea::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'smr_areas';
    }
}
