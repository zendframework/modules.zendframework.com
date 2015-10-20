<?php

use User\Entity;
use Zend\Db;
use Zend\Session;

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
            'ScnSocialAuth_ZendDbAdapter' => Db\Adapter\Adapter::class,
            'ScnSocialAuth_ZendSessionManager' => Session\SessionManager::class,
            'zfcuser_zend_db_adapter' => Db\Adapter\Adapter::class,
        ],
        'factories' => [
            Db\Adapter\Adapter::class => Db\Adapter\AdapterServiceFactory::class,
        ],
        'invokables' => [
            Session\SessionManager::class => Session\SessionManager::class,
        ],
    ],
    'zfcuser' => [
        'user_entity_class' => Entity\User::class,
        'logout_redirect_route' => 'home',
    ],
];
