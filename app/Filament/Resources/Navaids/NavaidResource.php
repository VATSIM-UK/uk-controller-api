<?php

namespace App\Filament\Resources\Navaids;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\Navaids\RelationManagers\HoldsRelationManager;
use App\Filament\Resources\Navaids\Pages\ListNavaids;
use App\Filament\Resources\Navaids\Pages\CreateNavaid;
use App\Filament\Resources\Navaids\Pages\ViewNavaid;
use App\Filament\Resources\Navaids\Pages\EditNavaid;
use App\Filament\Helpers\HasCoordinates;
use App\Filament\Resources\NavaidResource\Pages;
use App\Filament\Resources\NavaidResource\RelationManagers;
use App\Models\Navigation\Navaid;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\TranslatesStrings;

class NavaidResource extends Resource
{
    use TranslatesStrings;
    use HasCoordinates;

    protected static ?string $model = Navaid::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $recordTitleAttribute = 'identifier';
    protected static ?string $navigationLabel = 'Navaids and Holds';
    protected static ?string $label = 'Navaids and Holds';
    protected static string|\UnitEnum|null $navigationGroup = 'Enroute';

    public static function getEloquentQuery(): Builder
    {
        return Navaid::with('holds');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('identifier')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(5)
                    ->label(self::translateFormPath('identifier.label'))
                    ->helperText(self::translateFormPath('identifier.helper'))
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord),
                self::latitudeInput(),
                self::longitudeInput(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('identifier')
                    ->sortable()
                    ->searchable()
                    ->label(self::translateTablePath('columns.identifier')),
                IconColumn::make('has_published_holds')
                    ->getStateUsing(function (Navaid $record) {
                        return $record->holds->isNotEmpty();
                    })
                    ->label(self::translateTablePath('columns.published_holds'))
                    ->boolean(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])->defaultSort('identifier');
    }

    public static function getRelations(): array
    {
        return [
            HoldsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListNavaids::route('/'),
            'create' => CreateNavaid::route('/create'),
            'view' => ViewNavaid::route('/{record}'),
            'edit' => EditNavaid::route('/{record}/edit'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'navaids';
    }
}
