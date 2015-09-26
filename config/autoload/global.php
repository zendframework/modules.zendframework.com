<?php
return [
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
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'params' => [
                    'host'     => 'localhost',
                    'port'     => '3306',
                    'user'     => 'modules',
                    'password' => 'modules',
                    'dbname'   => 'modules',
                ],
            ],
        ],
        'migrations_configuration' => [
            'orm_default' => [
                'directory' => 'data/migrations',
                'name'      => 'ZFModules DBAL Migrations',
                'namespace' => 'ZfModulesMigrations',
                'table'     => 'doctrine_migration_versions',
            ],
        ],
    ],
];
