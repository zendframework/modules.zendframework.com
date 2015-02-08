<?php

use User\Controller;
use User\GitHub;

return [
    'controllers' => [
        'aliases' => [
            'zfcuser' => Controller\UserController::class,
        ],
        'factories' => [
            Controller\UserController::class => Controller\UserControllerFactory::class,
        ],
    ],
    'view_manager' => [
        'template_map' => [
            'scn-social-auth/user/login'    =>  __DIR__ . '/../view/scn-social-auth/user/login.phtml',
            'user/helper/new-users'         =>  __DIR__ . '/../view/user/helper/new-users.phtml',
            'zfc-user/user/index'           =>  __DIR__ . '/../view/zfc-user/user/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'newUsers' => 'User\View\Helper\NewUsersFactory',
            'userOrganizations' => 'User\View\Helper\UserOrganizationsFactory',
        ],
    ],
    'service_manager' => [
        'invokables' => [
            GitHub\LoginListener::class => GitHub\LoginListener::class,
        ],
    ],
];
