<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Aircraft\Aircraft;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class AirlinesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;
    
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'airlines';
    protected static ?string $inverseRelationship = 'stands';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')
                    ->label(self::translateTablePath('columns.icao'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('aircraft_id')
                    ->label(self::translateTablePath('columns.aircraft'))
                    ->formatStateUsing(fn (?int $state) => isset($state) ? Aircraft::find($state)->code : '')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign')),
                Tables\Columns\TextColumn::make('callsign_slug')
                    ->label(self::translateTablePath('columns.callsign_slug')),
                Tables\Columns\TextColumn::make('priority')
                    ->label(self::translateTablePath('columns.priority')),
                Tables\Columns\TextColumn::make('not_before')
                    ->label(self::translateTablePath('columns.not_before'))
                    ->date('H:i'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-airline')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        Select::make('aircraft_id')
                            ->options(SelectOptions::aircraftTypes())
                            ->searchable()
                            ->label(self::translateFormPath('aircraft.label'))
                            ->helperText(self::translateFormPath('aircraft.helper')),
                        TextInput::make('destination')
                            ->label(self::translateFormPath('destination.label'))
                            ->helperText(self::translateFormPath('destination.helper'))
                            ->maxLength(4),
                        TextInput::make('callsign')
                            ->label(self::translateFormPath('callsign.label'))
                            ->helperText(self::translateFormPath('callsign.helper'))
                            ->maxLength(4),
                        TextInput::make('callsign_slug')
                            ->label(self::translateFormPath('callsign_slug.label'))
                            ->helperText(self::translateFormPath('callsign_slug.helper'))
                            ->maxLength(4),
                        TextInput::make('priority')
                            ->label(self::translateFormPath('priority.label'))
                            ->helperText(self::translateFormPath('priority.helper'))
                            ->default(100)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->required(),
                        TimePicker::make('not_before')
                            ->label(self::translateFormPath('not_before.label'))
                            ->helperText(self::translateFormPath('not_before.helper'))
                            ->displayFormat('H:i')
                            ->afterStateUpdated(function (Closure $get, Closure $set) {
                                if ($get('not_before') !== null) {
                                    $set(
                                        'not_before',
                                        Carbon::parse($get('not_before'))->startOfMinute()->toDateTimeString()
                                    );
                                }
                            }),
                    ])
            ])
            ->actions([
                Tables\Actions\DetachAction::make('unpair-airline')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(function (Tables\Actions\DetachAction $action) {
                        DB::table('airline_stand')
                            ->where('id', $action->getRecord()->pivot_id)
                            ->delete();
                    })
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'stands.airlines';
    }
}
