<?php

namespace App\Filament\Resources\Aircraft\RelationManagers;

use Filament\Tables\Columns\TextColumn;
use Filament\Actions\AttachAction;
use Filament\Actions\DetachAction;
use App\Filament\Resources\Pages\LimitsTableRecordListingOptions;
use App\Filament\Resources\TranslatesStrings;
use App\Models\Aircraft\WakeCategory;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;
use Filament\Tables;

class WakeCategoriesRelationManager extends RelationManager
{
    use LimitsTableRecordListingOptions;
    use TranslatesStrings;

    protected static string $relationship = 'wakeCategories';
    protected static ?string $inverseRelationship = 'aircraft';
    protected static ?string $recordTitleAttribute = 'description';

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheme.name')
                    ->label(self::translateTablePath('columns.scheme_name')),
                TextColumn::make('code')
                    ->label(self::translateTablePath('columns.code')),
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
            ])
            ->headerActions([
                AttachAction::make()
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('attach'))
                    ->preloadRecordSelect()
                    ->recordTitle(fn (WakeCategory $record) => sprintf('%s: %s', $record->scheme->name, $record->description))
                    ->form(fn (AttachAction $action) => [
                        $action->getRecordSelect()
                            ->label(self::translateFormPath('category.label'))
                            ->required(),
                    ]),
            ])
            ->recordActions([
                DetachAction::make()
                    ->authorize(fn (RelationManager $livewire) => $livewire->can('detach')),
            ]);
    }

    protected static function translationPathRoot(): string
    {
        return 'aircraft.wake_categories';
    }
}
