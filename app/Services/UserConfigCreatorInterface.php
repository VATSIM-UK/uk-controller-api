<?php

namespace App\Services;

use App\Helpers\User\UserConfig;

interface UserConfigCreatorInterface
{
    public function create(int $cid): UserConfig;
}
