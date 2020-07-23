<?php

use App\Models\User\User;
use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('minstack-updates', function (User $user) {
    return $user->exists();
});

Broadcast::channel('hold-assignments', function (User $user) {
    return $user->exists();
});

Broadcast::channel('rps-updates', function (User $user) {
    return $user->exists();
});

Broadcast::channel('controller_{position}', function (User $user) {
    return $user->exists();
});
