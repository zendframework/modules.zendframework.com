<?php
return array(
    'controllers' => array(
        'invokables' => array(
            'ZfModule\Controller\Index' => 'ZfModule\Controller\IndexController',
            'ZfModule\Controller\Repo' => 'ZfModule\Controller\RepoController',
        ),
    ),
    'router' => array(
        'routes' => array(
            'view-module' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:vendor/:module',
                    'defaults' => array(
                        'controller' => 'ZfModule\Controller\Index',
                        'action' => 'view',
                    ),
                ),
            ),
            'zf-module' => array(
                'type' => 'Segment',
                'options' => array (
                    'route' => '/module',
                    'defaults' => array(
                        'controller' => 'ZfModule\Controller\Index',
                        'action' => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'list' => array(
                        'type' => 'Segment',
                        'options' => array(
                            'route' => '/list[/:owner]',
                            'constrains' => array(
                                'owner' => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                                'action' => 'organization',
                            ),
                        ),
                    ),
                    'add' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/add',
                            'defaults' => array(
                                'action' => 'add',
                            ),
                        ),
                    ),
                    'remove' => array(
                        'type' => 'Literal',
                        'options' => array(
                            'route' => '/remove',
                            'defaults' => array(
                                'action' => 'remove',
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            'zf-module' => __DIR__ . '/../view',
        ),
    ),

    'view_helpers' => array(
        'factories' => array(
            'listModule' => 'ZfModule\View\Helper\ListModuleFactory',
            'newModule' => 'ZfModule\View\Helper\NewModuleFactory',
            'totalModules' => 'ZfModule\View\Helper\TotalModulesFactory',
        ),
        'invokables' => array(
            'moduleView' => 'ZfModule\View\Helper\ModuleView',
            'moduleDescription' => 'ZfModule\View\Helper\ModuleDescription',
            'composerView' => 'ZfModule\View\Helper\ComposerView',
        ),
    ),
    'zfmodule' => array(
        /**
         * Cache configuration
         */
        'cache' => array(
            'adapter'   => array(
                'name' => 'filesystem',
                'options' => array(
                    'cache_dir' => realpath('./data/cache'),
                    'writable' => false,
                ),
            ),
            'plugins' => array(
                'exception_handler' => array('throw_exceptions' => true),
                'serializer'
            )
        ),
        'cache_key' => 'zfmodule_app',
    ),
);
