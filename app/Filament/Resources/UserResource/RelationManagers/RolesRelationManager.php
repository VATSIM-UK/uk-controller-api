<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use Closure;
use Filament\Resources\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Resources\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class RolesRelationManager extends RelationManager
{
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'description';

    protected function getTableDescription(): ?string
    {
        return __('table.users.roles.description');
    }

    public static function form(Form $form): Form
    {
        return $form;
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(__('table.users.roles.attach_action.trigger_button'))
                    ->modalHeading(__('table.users.roles.attach_action.modal_heading'))
                    ->modalButton(__('table.users.roles.attach_action.confirm_button'))
                    ->disableAttachAnother()
                    ->preloadRecordSelect()
                    ->hidden(self::hideActionsClosure()),
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label(__('table.users.roles.detach_action.trigger_button'))
                    ->modalHeading(
                        fn (Tables\Actions\DetachAction $action) => __(
                            'table.users.roles.detach_action.modal_heading',
                            ['role' => $action->getRecordTitle()]
                        )
                    )
                    ->modalButton(__('table.users.roles.detach_action.confirm_button'))
                    ->hidden(self::hideActionsClosure()),
            ]);
    }

    private static function hideActionsClosure(): Closure
    {
        return fn (RolesRelationManager $livewire) => $livewire->getOwnerRecord()->id === Auth::id();
    }
}
