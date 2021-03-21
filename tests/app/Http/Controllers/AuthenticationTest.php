<?php

namespace App\Http\Controllers;

use App\BaseApiTestCase;
use App\Models\User\Admin;
use App\Providers\RouteServiceProvider;

class AuthenticationTest extends BaseApiTestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->app['env'] = 'testing';
    }

    public function testLoginScreenCanBeRendered()
    {
        $response = $this->get('web/login');
        $response->assertStatus(200);
    }

    public function testUsersCanAuthenticateUsingTheLoginScreen()
    {
        $response = $this->post('web/login', [
            'email' => Admin::find(1203533)->email,
            'password' => 'letmein',
        ]);

        $this->assertAuthenticated('web_admin');
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function testUsersCannotAuthenticateWithInvalidPassword()
    {
        $this->post('web/login', [
            'email' => Admin::find(1203533)->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }
}
