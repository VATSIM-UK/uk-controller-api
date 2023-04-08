<?php

namespace App\Filament\Resources\AirlineResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Stand\Stand;
use Carbon\Carbon;
use Closure;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Filament\Tables\Actions\DetachAction;
use Illuminate\Support\Facades\DB;

class StandsRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    private const DEFAULT_COLUMN_VALUE = '--';
    protected bool $allowsDuplicates = true;
    protected static string $relationship = 'stands';
    protected static ?string $inverseRelationship = 'airlines';
    protected static ?string $recordTitleAttribute = 'identifier';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('stand_id')
                    ->formatStateUsing(fn (Stand $record) => $record->airfieldIdentifier)
                    ->label(self::translateTablePath('columns.terminal'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('destination')
                    ->label(self::translateTablePath('columns.destination'))
                    ->default(self::DEFAULT_COLUMN_VALUE)
                    ->sortable(),
                Tables\Columns\TextColumn::make('callsign_slug')
                    ->default(self::DEFAULT_COLUMN_VALUE)
                    ->label(self::translateTablePath('columns.callsign')),
                Tables\Columns\TextColumn::make('priority')
                    ->default(self::DEFAULT_COLUMN_VALUE)
                    ->label(self::translateTablePath('columns.priority')),
                Tables\Columns\TextColumn::make('not_before')
                    ->label(self::translateTablePath('columns.not_before'))
                    ->date('H:i'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make('pair-stand')
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action
                            ->recordTitle(fn (Stand $record):string => $record->airfieldIdentifier)
                            ->getRecordSelect()
                            ->label(self::translateFormPath('icao.label'))
                            ->required(),
                        TextInput::make('destination')
                            ->label(self::translateFormPath('destination.label'))
                            ->helperText(self::translateFormPath('destination.helper'))
                            ->maxLength(4),
                        TextInput::make('callsign_slug')
                            ->label(self::translateFormPath('callsign.label'))
                            ->helperText(self::translateFormPath('callsign.helper'))
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
                DetachAction::make('unpair-stand')
                    ->label(self::translateFormPath('remove.label'))
                    ->using(function (DetachAction $action) {
                        DB::table('airline_stand')
                            ->where('id', $action->getRecord()->pivot_id)
                            ->delete();
                    })
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'airlines.stands';
    }
}
