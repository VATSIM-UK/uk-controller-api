<?php

namespace App\Filament\Resources\SidResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;

class PrenotesRelationManager extends RelationManager
{
    protected static string $relationship = 'prenotes';

    protected static ?string $recordTitleAttribute = 'description';

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description')
                    ->label(__('table.sids.prenotes.columns.description')),
                Tables\Columns\TagsColumn::make('controllers.callsign')
                    ->label(__('table.sids.prenotes.columns.controllers')),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->form(fn (Tables\Actions\AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(__('form.stands.airlines.icao.label'))
                            ->required(),
                    ])
                    ->label(__('table.sids.prenotes.attach_action.trigger_button'))
                    ->modalHeading(__('table.sids.prenotes.attach_action.modal_heading'))
                    ->modalButton(__('table.sids.prenotes.attach_action.confirm_button'))
                    ->disableAttachAnother(),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label(__('table.sids.prenotes.detach_action.trigger_button'))
                    ->modalHeading(
                        fn (Tables\Actions\DetachAction $action) => __(
                            'table.sids.prenotes.detach_action.modal_heading',
                            ['prenote' => $action->getRecordTitle()]
                        )
                    )
                    ->modalButton(__('table.sids.prenotes.detach_action.confirm_button'))
            ]);
    }
}
