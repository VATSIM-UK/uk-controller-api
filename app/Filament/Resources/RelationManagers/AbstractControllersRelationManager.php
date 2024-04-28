<?php

namespace App\Filament\Resources\RelationManagers;

use App\Filament\Resources\TranslatesStrings;
use App\Helpers\Controller\FrequencyFormatter;
use App\Models\Controller\ControllerPosition;
use App\Services\ControllerPositionHierarchyService;
use Filament\Forms;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * Abstract relations manager for managing controller hierarchies
 * in many-many relations.
 */
abstract class AbstractControllersRelationManager extends RelationManager
{
    use TranslatesStrings;

    protected static string $relationship = 'controllers';
    protected static ?string $recordTitleAttribute = 'callsign';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('order')
                    ->label(self::translateTablePath('columns.order.label')),
                Tables\Columns\TextColumn::make('callsign')
                    ->label(self::translateTablePath('columns.callsign.label')),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(self::translateTablePath('columns.frequency.label'))
                    ->formatStateUsing(fn (float $state) => FrequencyFormatter::formatFrequency($state)),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action, AbstractControllersRelationManager $livewire) => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('insert_after')
                            ->label(self::translateTablePath('attach_form.insert_after.label'))
                            ->helperText(self::translateTablePath('attach_form.insert_after.helper'))
                            ->options(
                                $livewire->getOwnerRecord()
                                    ->controllers
                                    ->mapWithKeys(
                                        fn (ControllerPosition $controller) =>
                                            [$controller->id => $controller->callsign]
                                    )
                            ),
                    ])
                    ->using(function (AbstractControllersRelationManager $livewire, $data) {
                        self::doUpdate(
                            fn () => ControllerPositionHierarchyService::insertPositionIntoHierarchy(
                                $livewire->getOwnerRecord(),
                                ControllerPosition::findOrFail($data['recordId']),
                                after: isset($data['insert_after'])
                                    ? ControllerPosition::findOrFail($data['insert_after'])
                                    : null
                            ),
                            $livewire->getOwnerRecord()
                        );
                    })
                    ->disableAttachAnother()
                    ->label(self::translateTablePath('attach_action.label'))
                    ->modalHeading(self::translateTablePath('attach_action.modal_heading'))
                    ->modalButton(self::translateTablePath('attach_action.modal_button')),
            ])
            ->actions([
                Tables\Actions\Action::make('moveUp')
                    ->action(function (ControllerPosition $record) {
                        self::doUpdate(
                            fn () => ControllerPositionHierarchyService::moveControllerInHierarchy(
                                $record->pivot->pivotParent,
                                $record,
                                true
                            ),
                            $record->pivot->pivotParent
                        );
                    })
                    ->label(self::translateTablePath('move_up_action.label'))
                    ->icon('heroicon-o-arrow-up')
                    ->authorize(fn (AbstractControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\Action::make('moveDown')
                    ->action(function (ControllerPosition $record) {
                        self::doUpdate(
                            fn () => ControllerPositionHierarchyService::moveControllerInHierarchy(
                                $record->pivot->pivotParent,
                                $record,
                                false
                            ),
                            $record->pivot->pivotParent
                        );
                    })
                    ->label(self::translateTablePath('move_down_action.label'))
                    ->icon('heroicon-o-arrow-down')
                    ->authorize(fn (AbstractControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\DetachAction::make()
                    ->using(function (ControllerPosition $record) {
                        self::doUpdate(
                            fn () => ControllerPositionHierarchyService::removeFromHierarchy(
                                $record->pivot->pivotParent,
                                $record
                            ),
                            $record->pivot->pivotParent
                        );
                    })->label(self::translateTablePath('detach_action.label')),
            ]);
    }

    private static function doUpdate(callable $update, Model $ownerRecord)
    {
        DB::transaction(function () use ($update, $ownerRecord) {
            $update();
            $ownerRecord->load('controllers');
            static::postUpdate($ownerRecord);
        });
    }

    protected static function postUpdate(Model $ownerRecord): void
    {
    }
}
