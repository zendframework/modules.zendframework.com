<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2012 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

use Application\Service;
use Application\View;

return [
    'router' => [
        'routes' => [
            'live-search' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/live-search',
                    'defaults' => [
                        'controller' => 'Application\Controller\Search',
                        'action'     => 'index',
                    ],
                ],
            ],
            'home' => [
                'type' => 'Segment',
                'options' => [
                    'route'    => '/[page/:page]',
                    'constraints' => [
                        'page'     => '[0-9]+',
                    ],
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ],
                ],
                'priority' => 1,
                'may_terminate' => true,
            ],
            'feed' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/feed',
                    'defaults' => [
                        'controller' => 'Application\Controller\Index',
                        'action' => 'feed',
                    ],
                ],
            ],

            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/application',
                    'defaults' => [
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ],
                ],
                'may_terminate' => true,
                'child_routes' => [
                    'default' => [
                        'type'    => 'Segment',
                        'options' => [
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => [
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ],
                            'defaults' => [
                            ],
                        ],
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            'Application\Controller\Index' => 'Application\Controller\IndexControllerFactory',
            'Application\Controller\Search' => 'Application\Controller\SearchControllerFactory',
        ],
    ],
    'service_manager' => [
        'factories' => [
            'translator' => 'Zend\I18n\Translator\TranslatorServiceFactory',
            Service\RepositoryRetriever::class => Service\RepositoryRetrieverFactory::class,
            \HTMLPurifier::class => Service\HtmlPurifierFactory::class,
        ],
    ],
    'translator' => [
        'locale' => 'en_US',
        'translation_patterns' => [
            [
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ],
        ],
    ],
    'asset_manager' => [
        'resolver_configs' => [
            'paths' => [
                __DIR__ . '/../public',
            ],
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'layout/error'                      => __DIR__ . '/../view/layout/layout-small-header.phtml',
            'layout/layout'                     => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index'           => __DIR__ . '/../view/application/index/index.phtml',
            'application/index/pagination'      => __DIR__ . '/../view/application/index/pagination.phtml',
            'error/404'                         => __DIR__ . '/../view/error/404.phtml',
            'error/index'                       => __DIR__ . '/../view/error/index.phtml',
            'application/helper/new-modules'    => __DIR__ . '/../view/application/helper/new-modules.phtml',
            'application/search/index'          => __DIR__ . '/../view/application/search/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewFeedStrategy',
        ],
    ],
    'view_helpers' => [
        'factories' => [
            'sanitizeHtml' => View\Helper\SanitizeHtmlFactory::class,
            'flashMessenger' => function ($sm) {
                $sm = $sm->getServiceLocator();
                $plugin = $sm->get('ControllerPluginManager')->get('flashMessenger');

                $helper = new View\Helper\FlashMessenger($plugin);
                return $helper;
            }
        ]
    ],
];
