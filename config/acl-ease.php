<?php

return [
    # Models required
    'models' => [
        'user' => \App\Models\User::class,
        'role' => \SachinKiranti\AclEase\Models\Role::class,
        'permission' => \SachinKiranti\AclEase\Models\Permission::class,
    ],
];
