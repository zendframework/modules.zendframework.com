<?php

use EdpGithub\Client;
use ZfModule\Controller;
use ZfModule\Delegators\EdpGithubClientAuthenticator;
use ZfModule\Mapper\ModuleHydrator;
use ZfModule\Mvc;
use ZfModule\View\Helper;

return [
    'controller_plugins' => [
        'factories' => [
            'listModule' => Mvc\Controller\Plugin\ListModuleFactory::class,
        ],
    ],
    'controllers'  => [
        'factories' => [
            Controller\IndexController::class => Controller\IndexControllerFactory::class,
        ],
    ],
    'router'       => [
        'routes' => [
            'view-module' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/:vendor/:module',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'view',
                    ],
                ],
            ],
            'zf-module'   => [
                'type'          => 'Segment',
                'options'       => [
                    'route'    => '/module',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes'  => [
                    'list'   => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'      => '/list[/:owner]',
                            'constrains' => [
                                'owner' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults'   => [
                                'action' => 'organization',
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
            'moduleView'        => Helper\ModuleView::class,
            'moduleDescription' => Helper\ModuleDescription::class,
            'composerView'      => Helper\ComposerView::class,
        ],
    ],
    'service_manager' => [
        'invokables' => [
            ModuleHydrator::class => ModuleHydrator::class,
        ],
        'factories' => [
            'zfmodule_service_module' => ZfModule\Service\ModuleFactory::class,
            'zfmodule_mapper_module' => ZfModule\Mapper\ModuleFactory::class,
        ],
        'delegators' => [
            Client::class => [
                EdpGithubClientAuthenticator::class,
            ],
        ],
    ],
];
