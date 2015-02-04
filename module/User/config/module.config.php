<?php

use User\GitHub;

return [
    'view_manager' => [
        'template_map' => [
            'helper/module'                 =>  __DIR__ . '/../view/helper/module.phtml',
            'scn-social-auth/user/login'    =>  __DIR__ . '/../view/scn-social-auth/user/login.phtml',
            'user/helper/new-users'         =>  __DIR__ . '/../view/user/helper/new-users.phtml',
            'user/module/orgs'              =>  __DIR__ . '/../view/user/module/orgs.phtml',
            'user/module/repos'             =>  __DIR__ . '/../view/user/module/repos.phtml',
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
