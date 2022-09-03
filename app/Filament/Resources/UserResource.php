<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User\User;
use App\Models\User\UserStatus;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Form;
use Filament\Resources\Resource;
use Filament\Resources\Table;
use Filament\Tables;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $recordTitleAttribute = 'name';

    public static function canGloballySearch(): bool
    {
        return false;
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Administration';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('id')
                    ->label(__('form.users.cid.label'))
                    ->disabled(),
                TextInput::make('first_name')
                    ->label(__('form.users.first_name.label'))
                    ->helperText(__('form.users.first_name.helper'))
                    ->disabled(),
                TextInput::make('last_name')
                    ->label(__('form.users.last_name.label'))
                    ->helperText(__('form.users.last_name.helper'))
                    ->disabled(),
                Select::make('status')
                    ->options(
                        fn () => UserStatus::all()->mapWithKeys(
                            fn (UserStatus $status) => [$status->id => $status->statusMessage()]
                        )
                    )
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('table.users.columns.id'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('table.users.columns.name'))
                    ->searchable(['user.first_name', 'user.last_name']),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('table.users.columns.status'))
                    ->formatStateUsing(fn (int $state) => UserStatus::find($state)?->statusMessage()),
                Tables\Columns\TagsColumn::make('roles.description')
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\RolesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUser::route('/{record}'),
        ];
    }
}
