<?php

namespace App\Filament\Resources\PrenoteResource\RelationManagers;

use App\Helpers\Controller\FrequencyFormatter;
use App\Models\Controller\ControllerPosition;
use App\Services\PrenoteService;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class ControllersRelationManager extends RelationManager
{
    protected static string $relationship = 'controllers';
    protected static ?string $recordTitleAttribute = 'callsign';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('callsign')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label(__('table.prenotes.controller_positions.columns.order.label')),
                Tables\Columns\TextColumn::make('callsign')
                    ->label(__('table.prenotes.controller_positions.columns.callsign.label')),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(__('table.prenotes.controller_positions.columns.frequency.label'))
                    ->formatStateUsing(fn (float $state) => FrequencyFormatter::formatFrequency($state)),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action, ControllersRelationManager $livewire) => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('insert_after')
                            ->label(__('table.prenotes.controller_positions.attach_form.insert_after.label'))
                            ->helperText(
                                __('table.prenotes.controller_positions.attach_form.insert_after.helper')
                            )
                            ->options(
                                $livewire->getOwnerRecord()
                                    ->controllers
                                    ->mapWithKeys(
                                        fn (ControllerPosition $controller) => [$controller->id => $controller->callsign]
                                    )
                            ),
                    ])
                    ->using(function (ControllersRelationManager $livewire, $data) {
                        PrenoteService::insertPositionIntoPrenoteOrder(
                            $livewire->getOwnerRecord(),
                            ControllerPosition::findOrFail($data['recordId']),
                            after: isset($data['insert_after'])
                                ? ControllerPosition::findOrFail($data['insert_after'])
                                : null
                        );
                    })
                    ->disableAttachAnother()
                    ->label(__('table.prenotes.controller_positions.attach_action.label'))
                    ->modalHeading(__('table.prenotes.controller_positions.attach_action.modal_heading'))
                    ->modalButton(__('table.prenotes.controller_positions.attach_action.modal_button')),
            ])
            ->actions([
                Tables\Actions\Action::make('moveUp')
                    ->action(function (ControllerPosition $record) {
                        PrenoteService::moveControllerInPrenoteOrder(
                            $record->pivot->pivotParent,
                            $record,
                            true
                        );
                    })
                    ->label(__('table.prenotes.controller_positions.move_up_action.label'))
                    ->icon('heroicon-o-arrow-up')
                    ->authorize(fn (ControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\Action::make('moveDown')
                    ->action(function (ControllerPosition $record) {
                        PrenoteService::moveControllerInPrenoteOrder(
                            $record->pivot->pivotParent,
                            $record,
                            false
                        );
                    })
                    ->label(__('table.prenotes.controller_positions.move_down_action.label'))
                    ->icon('heroicon-o-arrow-down')
                    ->authorize(fn (ControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\DetachAction::make()
                    ->using(function (ControllerPosition $record) {
                        PrenoteService::removeFromPrenoteOrder(
                            $record->pivot->pivotParent,
                            $record
                        );
                    })->label(__('table.prenotes.controller_positions.detach_action.label')),
            ]);
    }    
}
