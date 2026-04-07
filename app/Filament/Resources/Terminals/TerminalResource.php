<?php

namespace App\Filament\Resources\Terminals;

use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\ViewAction;
use Filament\Actions\EditAction;
use App\Filament\Resources\Terminals\Pages\ListTerminals;
use App\Filament\Resources\Terminals\Pages\CreateTerminal;
use App\Filament\Resources\Terminals\Pages\EditTerminal;
use App\Filament\Resources\Terminals\Pages\ViewTerminal;
use App\Filament\Helpers\SelectOptions;
use App\Filament\Resources\TerminalResource\Pages;
use App\Filament\Resources\Terminals\RelationManagers\AirlinesRelationManager;
use App\Models\Airfield\Terminal;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Resources\Pages\Page;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Filament\Tables;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;

class TerminalResource extends Resource
{
    use TranslatesStrings;

    protected static ?string $model = Terminal::class;
    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-command-line';
    protected static string | \UnitEnum | null $navigationGroup = 'Airfield';
    protected static ?string $recordTitleAttribute = 'description';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('airfield_id')
                    ->required()
                    ->label(self::translateFormPath('airfield.label'))
                    ->options(SelectOptions::airfields())
                    ->searchable(!App::runningUnitTests())
                    ->disabled(fn (Page $livewire) => !$livewire instanceof CreateRecord)
                    ->dehydrated(fn (Page $livewire) => $livewire instanceof CreateRecord)
                    ->required(),
                TextInput::make('description')
                    ->required()
                    ->label(self::translateFormPath('description.label'))
                    ->minLength(1)
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('airfield.code')
                    ->label(self::translateTablePath('columns.airfield'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('description')
                    ->label(self::translateTablePath('columns.description')),
                TextColumn::make('airlines_count')
                    ->label(self::translateTablePath('columns.airlines'))
                    ->counts('airlines'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
    }
    
    public static function getRelations(): array
    {
        return [
            AirlinesRelationManager::class,
        ];
    }
    
    public static function getPages(): array
    {
        return [
            'index' => ListTerminals::route('/'),
            'create' => CreateTerminal::route('/create'),
            'edit' => EditTerminal::route('/{record}/edit'),
            'view' => ViewTerminal::route('/{record}'),
        ];
    }

    protected static function translationPathRoot(): string
    {
        return 'terminals';
    }
}
