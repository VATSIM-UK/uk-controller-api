<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FirExitPointResource\Pages;
use App\Models\IntentionCode\FirExitPoint;
use App\Rules\Heading\ValidHeading;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;

class FirExitPointResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = FirExitPoint::class;

    protected static ?string $navigationIcon = 'heroicon-o-collection';
    protected static ?string $navigationGroup = 'Intention Codes';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('exit_point')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(5)
                    ->label(self::translateFormPath('exit_point.label'))
                    ->helperText(self::translateFormPath('exit_point.helper')),
                Toggle::make('internal')
                    ->label(self::translateFormPath('internal.label'))
                    ->helperText(self::translateFormPath('internal.helper'))
                    ->required(),
                Section::make('exit_cone')
                    ->heading(self::translateFormPath('exit_cone.heading'))
                    ->description(self::translateFormPath('exit_cone.description'))
                    ->schema([
                        TextInput::make('exit_direction_start')
                            ->required()
                            ->rule(new ValidHeading())
                            ->label(self::translateFormPath('exit_direction_start.label'))
                            ->helperText(self::translateFormPath('exit_direction_start.helper')),
                        TextInput::make('exit_direction_end')
                            ->required()
                            ->rule(new ValidHeading())
                            ->label(self::translateFormPath('exit_direction_end.label'))
                            ->helperText(self::translateFormPath('exit_direction_end.helper')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('exit_point')
                    ->label(self::translateTablePath('columns.exit_point'))
                    ->sortable()
                    ->searchable(),
                IconColumn::make('internal')
                    ->boolean()
                    ->label(self::translateTablePath('columns.internal')),
                TextColumn::make('exit_direction_start')
                    ->label(self::translateTablePath('columns.exit_direction_start')),
                TextColumn::make('exit_direction_end')
                    ->label(self::translateTablePath('columns.exit_direction_end')),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageFirExitPoints::route('/'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'fir_exit_points';
    }
}
