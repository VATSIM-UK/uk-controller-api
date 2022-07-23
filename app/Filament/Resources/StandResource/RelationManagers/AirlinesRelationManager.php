<?php

namespace App\Filament\Resources\StandResource\RelationManagers;

use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class AirlinesRelationManager extends RelationManager
{
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'airlines';
    protected static ?string $inverseRelationship = 'stands';

    protected static ?string $recordTitleAttribute = 'icao_code';

    protected function getTableDescription(): ?string
    {
        return __('table.stands.airlines.description');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('icao_code')
                    ->label(__('table.stands.airlines.columns.icao'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(__('table.stands.airlines.columns.destination'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('callsign_slug')
                    ->label(__('table.stands.airlines.columns.callsign')),
                Tables\Columns\TextColumn::make('priority')
                    ->label(__('table.stands.airlines.columns.priority')),
                Tables\Columns\TextColumn::make('not_before')
                    ->label(__('table.stands.airlines.columns.not_before'))
                    ->date('H:i'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-airline')
                    ->form(fn(Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(__('form.stands.airlines.icao.label'))
                            ->required(),
                        TextInput::make('destination')
                            ->label(__('form.stands.airlines.destination.label'))
                            ->helperText(__('form.stands.airlines.destination.helper'))
                            ->maxLength(4),
                        TextInput::make('callsign_slug')
                            ->label(__('form.stands.airlines.callsign.label'))
                            ->helperText(__('form.stands.airlines.callsign.helper'))
                            ->maxLength(4),
                        TextInput::make('priority')
                            ->label(__('form.stands.airlines.priority.label'))
                            ->helperText(__('form.stands.airlines.priority.helper'))
                            ->default(100)
                            ->numeric()
                            ->minValue(1)
                            ->maxValue(9999)
                            ->required(),
                        TimePicker::make('not_before')
                            ->label(__('form.stands.airlines.not_before.label'))
                            ->helperText(__('form.stands.airlines.not_before.helper'))
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
                    ->label(__('form.stands.airlines.remove.label'))
                    ->using(function (Tables\Actions\DetachAction $action) {
                        DB::table('airline_stand')
                            ->where('id', $action->getRecord()->pivot_id)
                            ->delete();
                    })
            ]);
    }
}
