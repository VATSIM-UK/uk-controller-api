<?php

namespace App\Filament\Resources\NotificationResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Controller\ControllerPosition;
use Closure;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
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

    protected static function translationPathRoot(): string
    {
        return 'notifications.controller_positions';
    }
}
