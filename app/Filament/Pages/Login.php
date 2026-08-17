<?php

namespace App\Filament\Pages;

use Filament\Actions\Action;
use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Schemas\Schema;
use Illuminate\Contracts\Support\Htmlable;

/**
 * @property Schema $form
 */
class Login extends \Filament\Auth\Pages\Login
{
    use InteractsWithForms;

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

    public function getHeading(): string|Htmlable
    {
        return 'UK Controller Plugin';
    }
}
