<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Laravel\Passport\Client;
use Laravel\Passport\Passport;

// Migrates oauth_clients to the Passport 13 schema (see laravel/passport#1912).
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->string('owner_type')->nullable()->after('user_id');
            $table->unsignedBigInteger('owner_id')->nullable()->after('owner_type');
            $table->text('redirect_uris')->nullable()->after('provider');
            $table->text('grant_types')->nullable()->after('redirect_uris');

            $table->index(['owner_type', 'owner_id']);
        });

        foreach (Passport::client()->newQuery()->cursor() as $client) {
            Model::withoutTimestamps(fn () => $client->forceFill([
                'owner_id' => $client->user_id,
                'owner_type' => $client->user_id
                    ? config('auth.providers.'.($client->provider ?: config('auth.guards.api.provider')).'.model')
                    : null,
                'redirect_uris' => $client->redirect_uris,
                'grant_types' => $this->resolveGrantTypes($client),
            ])->save());
        }

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->dropColumn(['user_id', 'redirect', 'personal_access_client', 'password_client']);
        });

        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->text('redirect_uris')->nullable(false)->change();
            $table->text('grant_types')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('oauth_clients', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->nullable()->after('id');
            $table->text('redirect')->nullable()->after('secret');
            $table->boolean('personal_access_client')->default(false)->after('redirect');
            $table->boolean('password_client')->default(false)->after('personal_access_client');

            $table->dropIndex(['owner_type', 'owner_id']);
            $table->dropColumn(['owner_type', 'owner_id', 'redirect_uris', 'grant_types']);
        });
    }

    private function resolveGrantTypes(Client $client): array
    {
        $attributes = $client->getAttributes();

        $grantTypes = array_values(array_filter([
            ! empty($attributes['personal_access_client']) ? 'personal_access' : null,
            ! empty($attributes['password_client']) ? 'password' : null,
        ]));

        return $grantTypes !== [] ? $grantTypes : $client->grant_types;
    }
};
