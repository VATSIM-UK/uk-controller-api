<?php

namespace App\Filament\Resources\HandoffResource\RelationManagers;

use App\Helpers\Controller\FrequencyFormatter;
use App\Models\Controller\ControllerPosition;
use App\Services\HandoffService;
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
                    ->label('#'),
                Tables\Columns\TextColumn::make('callsign'),
                Tables\Columns\TextColumn::make('frequency')
                    ->formatStateUsing(fn(float $state) => FrequencyFormatter::formatFrequency($state)),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn(Tables\Actions\AttachAction $action, ControllersRelationManager $livewire) => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('insert_after')
                            ->options(
                                $livewire->getOwnerRecord()
                                    ->controllers
                                    ->mapWithKeys(
                                        fn(ControllerPosition $controller) => [$controller->id => $controller->callsign]
                                    )
                            ),
                    ])
                    ->using(function (ControllersRelationManager $livewire, $data) {
                        $currentPositions = $livewire->getOwnerRecord()->controllers->pluck('callsign')->toArray();

                        if (!isset($data['insert_after'])) {
                            HandoffService::setPositionsForHandoffId(
                                $livewire->getOwnerRecord()->id,
                                array_merge(
                                    $currentPositions,
                                    [ControllerPosition::findOrFail($data['recordId'])->callsign]
                                )
                            );

                            return;
                        }

                        $insertAfterPosition = array_search(
                            ControllerPosition::findOrFail($data['insert_after'])->callsign,
                            $currentPositions
                        );

                        array_splice(
                            $currentPositions,
                            $insertAfterPosition + 1,
                            0,
                            [ControllerPosition::findOrFail($data['recordId'])->callsign]
                        );
                        HandoffService::setPositionsForHandoffId($livewire->getOwnerRecord()->id, $currentPositions);
                    })->disableAttachAnother(),
            ])
            ->actions([
                Tables\Actions\Action::make('moveUp')
                    ->action(function (ControllerPosition $record) {
                        $positions = $record->pivot->pivotParent->controllers->pluck('callsign')->toArray();
                        $thisPosition = array_search(
                            $record->callsign,
                            $positions
                        );

                        if ($thisPosition === 0) {
                            return;
                        }

                        $positionToSwap = $positions[$thisPosition - 1];
                        $positions[$thisPosition - 1] = $record->callsign;
                        $positions[$thisPosition] = $positionToSwap;

                        HandoffService::setPositionsForHandoffId($record->pivot->handoff_id, $positions);
                    })
                    ->label('Move Up')
                    ->icon('heroicon-o-arrow-up')
                    ->authorize(fn(ControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\Action::make('moveDown')
                    ->action(function (ControllerPosition $record) {
                        $positions = $record->pivot->pivotParent->controllers->pluck('callsign')->toArray();
                        $thisPosition = array_search(
                            $record->callsign,
                            $positions
                        );

                        if ($thisPosition === count($positions) - 1) {
                            return;
                        }

                        $positionToSwap = $positions[$thisPosition + 1];
                        $positions[$thisPosition + 1] = $record->callsign;
                        $positions[$thisPosition] = $positionToSwap;

                        HandoffService::setPositionsForHandoffId($record->pivot->handoff_id, $positions);
                    })
                    ->label('Move Down')
                    ->icon('heroicon-o-arrow-down')
                    ->authorize(fn(ControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\DetachAction::make()
                    ->using(function (ControllerPosition $record) {
                        HandoffService::removeFromHandoffOrderByModel(
                            $record->pivot->pivotParent,
                            $record->callsign
                        );
                    }),
            ]);
    }
}
