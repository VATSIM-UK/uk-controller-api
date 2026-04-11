<?php

namespace App\Filament\Resources\Sids\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TagsColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class PrenotesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    protected static string $relationship = 'prenotes';

    protected static ?string $recordTitleAttribute = 'description';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
                TextColumn::make('controllers.callsign')
                    ->label(self::translateTablePath('columns.controllers'))
                    ->badge(),
            ])
            ->headerActions([
                AttachAction::make()
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('attach'))
                    ->form(fn (AttachAction $action): array => [
                        $action->getRecordSelect()
                            ->label(__('form.stands.airlines.icao.label'))
                            ->required(),
                    ])
                    ->label(self::translateTablePath('attach_action.trigger_button'))
                    ->modalHeading(self::translateTablePath('attach_action.modal_heading'))
                    ->modalButton(self::translateTablePath('attach_action.confirm_button'))
                    ->disableAttachAnother(),
            ])
            ->recordActions([
                DetachAction::make()
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('detach'))
                    ->label(self::translateTablePath('detach_action.trigger_button'))
                    ->modalHeading(
                        fn (DetachAction $action) => __(
                            'table.sids.prenotes.detach_action.modal_heading',
                            ['prenote' => $action->getRecordTitle()]
                        )
                    )
                    ->modalButton(self::translateTablePath('detach_action.confirm_button')),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'sids.prenotes';
    }
}
