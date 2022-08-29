<?php

namespace App\Filament\Resources\NotificationResource\RelationManagers;

use App\Models\Controller\ControllerPosition;
use Closure;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\DB;

class ControllersRelationManager extends RelationManager
{
    protected static string $relationship = 'controllers';
    protected static ?string $recordTitleAttribute = 'callsign';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('callsign')
                    ->label(__('table.notifications.controller_positions.columns.callsign'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(__('table.notifications.controller_positions.columns.frequency')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('table.notifications.controller_positions.attach_action.label'))
                    ->modalHeading(__('table.notifications.controller_positions.attach_action.modal_heading'))
                    ->modalButton(__('table.notifications.controller_positions.attach_action.modal_button'))
                    ->disableAttachAnother()
                    ->form([
                        Forms\Components\Toggle::make('global')
                            ->label(__('table.notifications.controller_positions.attach_form.global.label'))
                            ->helperText(__('table.notifications.controller_positions.attach_form.global.helper'))
                            ->reactive()
                            ->afterStateUpdated(function (Closure $set) {
                                $set('controllers', null);
                            }),
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
                            ->hidden(fn (Closure $get) => $get('global'))
                            ->required(fn (Closure $get) => !$get('global')),
                    ])
                    ->using(function (ControllersRelationManager $livewire, array $data) {
                        DB::transaction(function () use ($livewire, $data) {
                            $livewire->getOwnerRecord()
                                ->controllers()
                                ->sync(
                                    $data['global']
                                        ? ControllerPosition::all()->pluck('id')
                                        : collect($data['controllers'])
                                        ->merge(
                                            $livewire->getOwnerRecord()->controllers()->pluck(
                                                'controller_positions.id'
                                            )
                                        )
                                        ->unique()
                                        ->values()
                                        ->toArray()
                                );
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
}
