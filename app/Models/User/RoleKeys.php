<?php

namespace App\Models\User;

enum RoleKeys: string
{
    case DIVISION_STAFF_GROUP = 'dsg';
    case WEB_TEAM = 'web_team';
    case OPERATIONS_TEAM = 'ops_team';
    case OPERATIONS_CONTRIBUTOR = 'ops_contributor';
    case VAA = 'vaa';
}
