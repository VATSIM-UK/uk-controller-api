<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use Closure;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Facades\Auth;

class RolesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;
    
    protected static string $relationship = 'roles';

    protected static ?string $recordTitleAttribute = 'description';

    protected function getTableDescription(): ?string
    {
        return self::translateTablePath('description');
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('description'),
            ])
            ->headerActions([
                Tables\Actions\AttachAction::make()
                    ->label(self::translateTablePath('attach_action.trigger_button'))
                    ->modalHeading(self::translateTablePath('attach_action.modal_heading'))
                    ->modalButton(self::translateTablePath('attach_action.confirm_button'))
                    ->hidden(self::hideActionsClosure())
                    ->disableAttachAnother()
                    ->preloadRecordSelect()
            ])
            ->actions([
                Tables\Actions\DetachAction::make()
                    ->label(self::translateTablePath('detach_action.trigger_button'))
                    ->modalHeading(
                        fn (Tables\Actions\DetachAction $action) => __(
                            'table.users.roles.detach_action.modal_heading',
                            ['role' => $action->getRecordTitle()]
                        )
                    )
                    ->modalButton(self::translateTablePath('detach_action.confirm_button'))
                    ->hidden(self::hideActionsClosure()),
            ]);
    }

    private static function hideActionsClosure(): Closure
    {
        return fn (RolesRelationManager $livewire) => $livewire->getOwnerRecord()->id === Auth::id();
    }

    protected static function translationPathRoot(): string
    {
        return 'users.roles';
    }
}
