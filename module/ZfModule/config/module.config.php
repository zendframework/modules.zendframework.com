<?php

use EdpGithub\Client;
use ZfModule\Controller;
use ZfModule\Delegators\EdpGithubClientAuthenticator;
use ZfModule\Mapper;
use ZfModule\Service;
use ZfModule\View\Helper;

return [
    'controllers'  => [
        'factories' => [
            Controller\ModuleController::class => Controller\ModuleControllerFactory::class,
            Controller\UserController::class => Controller\UserControllerFactory::class,
        ],
    ],
    'router'       => [
        'routes' => [
            'view-module' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/:vendor/:module',
                    'defaults' => [
                        'controller' => Controller\ModuleController::class,
                        'action'     => 'view',
                    ],
                ],
            ],
            'modules-for-user' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/user/:owner',
                    'constrains' => [
                        'owner' => '[a-zA-Z][a-zA-Z0-9_-]*',
                    ],
                    'defaults' => [
                        'controller' => Controller\UserController::class,
                        'action'     => 'modulesForUser',
                    ],
                ],
            ],
            'zf-module'   => [
                'type'          => 'Segment',
                'options'       => [
                    'route'    => '/module',
                    'defaults' => [
                        'controller' => Controller\ModuleController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'list'   => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'      => '/list[/:owner]',
                            'constraints' => [
                                'owner' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'   => [
                                'action' => 'list',
                            ],
                        ],
                    ],
                    'add'    => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/add',
                            'defaults' => [
                                'action' => 'add',
                            ],
                        ],
                    ],
                    'remove' => [
                        'type'    => 'Literal',
                        'options' => [
                            'route'    => '/remove',
                            'defaults' => [
                                'action' => 'remove',
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'view_manager' => [
        'template_path_stack' => [
            'zf-module' => __DIR__ . '/../view',
        ],
    ],
    'view_helpers' => [
        'factories'  => [
            'newModule'    => Helper\NewModuleFactory::class,
            'totalModules' => Helper\TotalModulesFactory::class,
        ],
        'invokables' => [
            'repository'        => Helper\Repository::class,
            'moduleDescription' => Helper\ModuleDescription::class,
            'composerView'      => Helper\ComposerView::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            Mapper\ModuleHydrator::class => Mapper\ModuleHydrator::class,
        ],
        'factories' => [
            Mapper\Module::class => Mapper\ModuleFactory::class,
            Service\Module::class => Service\ModuleFactory::class,
        ],
        'delegators' => [
            Client::class => [
                EdpGithubClientAuthenticator::class,
            ],
        ],
    ],
];
