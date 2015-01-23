<?php
use EdpGithub\Client;
use ZfModule\Delegators\EdpGithubClientAuthenticator;

return [
    'controllers'  => [
        'factories' => [
            'ZfModule\Controller\Index' => 'ZfModule\Controller\IndexControllerFactory',
        ],
    ],
    'router'       => [
        'routes' => [
            'view-module' => [
                'type'    => 'Segment',
                'options' => [
                    'route'    => '/:vendor/:module',
                    'defaults' => [
                        'controller' => 'ZfModule\Controller\Index',
                        'action'     => 'view',
                    ],
                ],
            ],
            'zf-module'   => [
                'type'          => 'Segment',
                'options'       => [
                    'route'    => '/module',
                    'defaults' => [
                        'controller' => 'ZfModule\Controller\Index',
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
            'listModule'   => 'ZfModule\View\Helper\ListModuleFactory',
            'newModule'    => 'ZfModule\View\Helper\NewModuleFactory',
            'totalModules' => 'ZfModule\View\Helper\TotalModulesFactory',
        ],
        'invokables' => [
            'moduleView'        => 'ZfModule\View\Helper\ModuleView',
            'moduleDescription' => 'ZfModule\View\Helper\ModuleDescription',
            'composerView'      => 'ZfModule\View\Helper\ComposerView',
        ],
    ],
    'service_manager' => [
        'delegators' => [
            Client::class => [
                EdpGithubClientAuthenticator::class,
            ],
        ],
    ],
];
