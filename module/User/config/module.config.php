<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'router' => array(
        'routes' => array(
            'zfcuser' => array(
                'may_terminate' => true,
                'child_routes' => array(
                    'organizations' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/organizations',
                            'defaults' => array(
                                'controller' => 'User\Controller\Module',
                                'action'     => 'organizations',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'User\Controller\Module' => 'User\Controller\ModuleController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'UserRepositories' => 'User\View\Helper\UserRepositories',
            'newUsers' => 'User\View\Helper\NewUsers',
        ),
    ),
);
