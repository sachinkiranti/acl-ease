<?php

return [
    # Middleware alias
    'middleware' => 'acl',

    # Default Role
    'default_role' => 'admin',

    'ignore_routes' => [
        // List of routes to be ignored.
    ],

    # Character to be used to glue permission slug
    'glue' => '_',

    # This characters will be replaced using glue while generating permission
    'black_listed_characters' => [
        '.',
    ],

    # Models required
    'models' => [
        'user' => \App\Models\User::class,
        'role' => \SachinKiranti\AclEase\Models\Role::class,
        'permission' => \SachinKiranti\AclEase\Models\Permission::class,
    ],
];
