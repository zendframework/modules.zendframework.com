<?php
return [
    'module_layouts' => [
        'User' => 'layout/layout-small-header.phtml',
        'ZfcUser' => 'layout/layout-small-header.phtml',
        'ZfModule' => 'layout/layout-small-header.phtml',
    ],
    'asset_manager' => [
        'caching' => [
            'default' => [
                'cache'     => 'FilePath',  // Apc, FilePath, FileSystem etc.
                'options' => [
                    'dir' => 'public',
                ],
            ],
        ],
    ],
    'service_manager' => [
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
        'invokables' => [
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ],
    ],
    'db' => [
        'driver'    => 'pdo',
        'dsn'       => 'mysql:dbname=modules;host=localhost',
        'username'  => 'modules',
        'password'  => 'modules',
    ],
];
