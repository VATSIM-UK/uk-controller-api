<?php

namespace App\Filament\Pages;

use App\Filament\Resources\TranslatesStrings;
use App\Models\User\User;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class UserPreferences extends Page
{
    use InteractsWithForms;
    use TranslatesStrings;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationGroup = 'Preferences';
    protected static ?string $navigationLabel = 'My Preferences';
    protected static ?string $title = 'My Preferences';
    protected static ?string $slug = 'my-preferences';

    protected static string $view = 'filament.pages.user-preferences';

    protected User $user;

    public function mount(): void
    {
        $this->form->fill([
            'send_stand_acars_messages' => $this->user->send_stand_acars_messages,
        ]);
    }

    protected function getFormSchema(): array
    {
        return [
            Toggle::make('send_stand_acars_messages')
                ->label(self::translateFormPath('user_preferences.acars.label'))
                ->helperText(self::translateFormPath('user_preferences.acars.helper'))
                ->reactive()
                ->afterStateUpdated(function () {
                    $this->submit();
                })
        ];
    }

    public function submit(): void
    {
        $this->getFormModel()->update($this->form->getState());
    }

    protected function getFormModel(): User
    {
        return tap(
            Auth::user(),
            function (User $user) {
                $this->user = $user;
            }
        );
    }

    protected static function translationPathRoot(): string
    {
        return 'stands';
    }
}
