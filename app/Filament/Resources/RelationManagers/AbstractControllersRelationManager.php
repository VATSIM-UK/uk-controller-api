<?php

namespace App\Filament\Resources\RelationManagers;

use App\Helpers\Controller\FrequencyFormatter;
use App\Models\Controller\ControllerPosition;
use App\Services\ControllerPositionHierarchyService;
use Filament\Forms;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

/**
 * Abstract relations manager for managing controller hierarchies
 * in many-many relations.
 */
abstract class AbstractControllersRelationManager extends RelationManager
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
                    ->label(self::tableTranslationString('controller_positions.columns.order.label')),
                Tables\Columns\TextColumn::make('callsign')
                    ->label(self::tableTranslationString('controller_positions.columns.callsign.label')),
                Tables\Columns\TextColumn::make('frequency')
                    ->label(self::tableTranslationString('controller_positions.columns.frequency.label'))
                    ->formatStateUsing(fn(float $state) => FrequencyFormatter::formatFrequency($state)),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn(Tables\Actions\AttachAction $action, AbstractControllersRelationManager $livewire) => [
                        $action->getRecordSelect(),
                        Forms\Components\Select::make('insert_after')
                            ->label(self::tableTranslationString('controller_positions.attach_form.insert_after.label'))
                            ->helperText(
                                self::tableTranslationString('controller_positions.attach_form.insert_after.helper')
                            )
                            ->options(
                                $livewire->getOwnerRecord()
                                    ->controllers
                                    ->mapWithKeys(
                                        fn(ControllerPosition $controller) => [$controller->id => $controller->callsign]
                                    )
                            ),
                    ])
                    ->using(function (AbstractControllersRelationManager $livewire, $data) {
                        ControllerPositionHierarchyService::insertPositionIntoHierarchy(
                            $livewire->getOwnerRecord(),
                            ControllerPosition::findOrFail($data['recordId']),
                            after: isset($data['insert_after'])
                                ? ControllerPosition::findOrFail($data['insert_after'])
                                : null
                        );
                    })
                    ->disableAttachAnother()
                    ->label(self::tableTranslationString('controller_positions.attach_action.label'))
                    ->modalHeading(self::tableTranslationString('controller_positions.attach_action.modal_heading'))
                    ->modalButton(self::tableTranslationString('controller_positions.attach_action.modal_button')),
            ])
            ->actions([
                Tables\Actions\Action::make('moveUp')
                    ->action(function (ControllerPosition $record) {
                        ControllerPositionHierarchyService::moveControllerInHierarchy(
                            $record->pivot->pivotParent,
                            $record,
                            true
                        );
                    })
                    ->label(self::tableTranslationString('controller_positions.move_up_action.label'))
                    ->icon('heroicon-o-arrow-up')
                    ->authorize(fn(AbstractControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\Action::make('moveDown')
                    ->action(function (ControllerPosition $record) {
                        ControllerPositionHierarchyService::moveControllerInHierarchy(
                            $record->pivot->pivotParent,
                            $record,
                            false
                        );
                    })
                    ->label(self::tableTranslationString('controller_positions.move_down_action.label'))
                    ->icon('heroicon-o-arrow-down')
                    ->authorize(fn(AbstractControllersRelationManager $livewire) => $livewire->can('moveUp')),
                Tables\Actions\DetachAction::make()
                    ->using(function (ControllerPosition $record) {
                        ControllerPositionHierarchyService::removeFromHierarchy(
                            $record->pivot->pivotParent,
                            $record
                        );
                    })->label(self::tableTranslationString('controller_positions.detach_action.label')),
            ]);
    }

    /**
     * Returns the root of the translation path for the relations manager, to build
     * labels etc.
     *
     * @return string
     */
    protected static abstract function translationPathRoot(): string;

    private static function tableTranslationString(string $path): string
    {
        return __(
            sprintf('table.%s.%s', static::translationPathRoot(), $path)
        );
    }

    private static function formTranslationString(string $path): string
    {
        return __(
            sprintf('form.%s.%s', static::translationPathRoot(), $path)
        );
    }
}
