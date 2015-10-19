<?php
return [
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
                'directory' => 'module/Application/src/Application/Doctrine/DBAL/Migration',
                'name'      => 'ZFModules DBAL Migrations',
                'namespace' => 'Application\Doctrine\DBAL\Migration',
                'table'     => 'doctrine_migration_versions',
            ],
        ],
    ],
    'htmlpurifier' => [
        'Cache.SerializerPath' => realpath('./data/cache'),
    ],
    'scn-social-auth' => [
        'github_enabled' => true,
        'github_scope' => 'user:email,read:org',
    ],
    'service_manager' => [
        'aliases' => [
            'ScnSocialAuth_ZendDbAdapter' => 'Zend\Db\Adapter\Adapter',
            'ScnSocialAuth_ZendSessionManager' => 'Zend\Session\SessionManager',
            'zfcuser_zend_db_adapter' => 'Zend\Db\Adapter\Adapter',
        ],
        'factories' => [
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ],
        'invokables' => [
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ],
    ],
    'zfcuser' => [
        'user_entity_class' => 'User\Entity\User',
        'logout_redirect_route' => 'home',
    ],
];
