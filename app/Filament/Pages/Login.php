<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Forms\ComponentContainer;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;

/**
 * @property ComponentContainer $form
 */
class Login extends BaseLogin
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.core-login';

    public function authenticate(): ?LoginResponse
    {
        $this->redirectRoute('vatsimuk.redirect');
        return null;
    }

    protected function getAuthenticateFormAction(): Action
    {
        return parent::getAuthenticateFormAction()
            ->label('Sign in via VATSIM UK Core');
    }

    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'UK Controller Plugin';
    }
}
