<?php

namespace App\Filament\Pages;

use App\Services\UserConfigCreatorInterface;
use Filament\Pages\Page;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class UserCreateKey extends Page
{
    protected static ?string $slug = 'user-create-api-key';
    protected static bool $shouldRegisterNavigation = false;

    public function mount(Request $request, UserConfigCreatorInterface $configCreator): void
    {
        $validator = Validator::make(
            array_map(
                fn (mixed $param) => urldecode($param),
                $request->query()
            ),
            [
                'redirect' => 'required|string|url',
            ]
        );

        abort_if(
            $validator->fails(),
            422
        );

        $userConfig = urlencode($configCreator->create(Auth::id())->apiKey());
        $this->redirect(sprintf('%s?key=%s', $validator->validated()['redirect'], $userConfig));
    }
}
