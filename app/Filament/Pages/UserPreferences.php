<?php

namespace App\Filament\Pages;

use Filament\Schemas\Schema;
use Filament\Schemas\Components\Fieldset;
use App\Filament\Resources\TranslatesStrings;
use App\Models\User\User;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class UserPreferences extends Page implements HasForms
{
    use InteractsWithForms;
    use TranslatesStrings;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-document-text';
    protected static string | \UnitEnum | null $navigationGroup = 'Preferences';
    protected static ?string $navigationLabel = 'My Preferences';
    protected static ?string $title = 'My Preferences';
    protected static ?string $slug = 'my-preferences';

    protected string $view = 'filament.pages.user-preferences';

    public ?array $data = [];

    public function mount(User $user): void
    {
        $this->form->fill(Auth::user()->toArray());
    }
    
    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Fieldset::make('stand_acars')
                ->model(fn () => Auth::user())
                ->label(self::translateFormPath('user_preferences.acars_heading.label'))
                ->schema([
                    Toggle::make('send_stand_acars_messages')
                        ->label(self::translateFormPath('user_preferences.acars.label'))
                        ->helperText(self::translateFormPath('user_preferences.acars.helper'))
                        ->reactive()
                        ->afterStateUpdated(function () {
                            $this->submit();
                        }),
                    Toggle::make('stand_acars_messages_uncontrolled_airfield')
                        ->label(self::translateFormPath('user_preferences.acars_uncontrolled.label'))
                        ->helperText(self::translateFormPath('user_preferences.acars_uncontrolled.helper'))
                        ->reactive()
                        ->afterStateUpdated(function () {
                            $this->submit();
                        }),
                ]),
        ])->statePath('data');
    }
    
    protected function submit() : void
    {
        Auth::user()->update($this->form->getState());
    }

    protected static function translationPathRoot(): string
    {
        return 'stands';
    }
}
