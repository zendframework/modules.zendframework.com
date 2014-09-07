<?php
return array(
    'module_layouts' => array(
        'ZfcUser' => 'layout/layout-small-header.phtml',
        'ZfModule' => 'layout/layout-small-header.phtml',
    ),
    'asset_manager' => array(
        'caching' => array(
            'default' => array(
                'cache'     => 'FilePath',  // Apc, FilePath, FileSystem etc.
                'options' => array(
                    'dir' => 'public',
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter\Adapter' => 'Zend\Db\Adapter\AdapterServiceFactory',
        ),
        'invokables' => array(
            'Zend\Session\SessionManager' => 'Zend\Session\SessionManager',
        ),
    ),
    'db' => array(
        'driver'    => 'pdo',
        'dsn'       => 'mysql:dbname=modules;host=localhost',
        'username'  => 'modules',
        'password'  => 'modules',
    ),
);
