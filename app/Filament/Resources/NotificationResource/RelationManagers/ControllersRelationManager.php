<?php

namespace App\Filament\Resources\NotificationResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Controller\ControllerPosition;
use Carbon\Carbon;
use Closure;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ControllersRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;

    use TranslatesStrings;

    protected static string $relationship = 'controllers';
    protected static ?string $recordTitleAttribute = 'callsign';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(self::translateTablePath('columns.frequency')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(self::translateTablePath('attach_action.label'))
                    ->modalHeading(self::translateTablePath('attach_action.modal_heading'))
                    ->modalButton(self::translateTablePath('attach_action.modal_button'))
                    ->disableAttachAnother()
                    ->form([
                        Forms\Components\Toggle::make('global')
                            ->label(self::translateTablePath('attach_form.global.label'))
                            ->helperText(self::translateTablePath('attach_form.global.helper'))
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set) {
                                $set('controllers', null);
                            }),
                        Forms\Components\MultiSelect::make('position_level')
                            ->options(
                                [
                                    'DEL' => 'Delivery',
                                    'GND' => 'Ground',
                                    'TWR' => 'Tower',
                                    'APP' => 'Approach',
                                    'CTR' => 'Enroute',
                                ]
                            )
                            ->reactive()
                            ->hidden(fn (Closure $get) => $get('global')),
                        Forms\Components\MultiSelect::make('controllers')
                            ->searchable()
                            ->options(
                                fn (ControllersRelationManager $livewire) => ControllerPosition::whereNotIn(
                                    'id',
                                    $livewire->getOwnerRecord()->controllers()->pluck('controller_positions.id')
                                )
                                    ->get()
                                    ->mapWithKeys(
                                        fn (
                                            ControllerPosition $controllerPosition
                                        ) => [$controllerPosition->id => $controllerPosition->callsign]
                                    )
                            )
                            ->hidden(fn (Closure $get) => $get('global') || $get('position_level'))
                            ->required(fn (Closure $get) => !$get('global') && !$get('position_level')),
                    ])
                    ->using(function (ControllersRelationManager $livewire, array $data) {
                        DB::transaction(function () use ($livewire, $data) {
                            $positionsToInsert = self::controllersForNotification($data)
                                ->merge(
                                    $livewire->getOwnerRecord()->controllers()->pluck(
                                        'controller_positions.id'
                                    )
                                )
                                ->unique()
                                ->values()
                                ->map(fn (int $positionId) => [
                                    'notification_id' => $livewire->getOwnerRecord()->id,
                                    'controller_position_id' => $positionId,
                                    'created_at' => Carbon::now(),
                                    'updated_at' => Carbon::now(),
                                ])
                                ->toArray();

                            DB::table('controller_position_notification')
                                ->where('notification_id', $livewire->getOwnerRecord()->id)
                                ->delete();
                            DB::table('controller_position_notification')
                                ->insert($positionsToInsert);
                        });
                    }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\DetachBulkAction::make(),
            ]);
    }

    private static function controllersForNotification(array $data): Collection
    {
        if ($data['global']) {
            return ControllerPosition::all()->pluck('id');
        }

        if (!empty($data['position_level'])) {
            $query = array_reduce(
                array_map(
                    fn (string $level) => ControllerPosition::where('callsign', 'like', '%' . $level),
                    $data['position_level']
                ),
                fn (?Builder $carry, Builder $positionQuery) => $carry ? $carry->union($positionQuery) : $positionQuery
            );

            return $query->get()->pluck('id');
        }

        return collect($data['controllers']);
    }

    protected static function translationPathRoot(): string
    {
        return 'notifications.controller_positions';
    }
}
