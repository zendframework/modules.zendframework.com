<?php

use Application\Controller;
use Application\Service;
use Application\View;
use Psr\Log;

return [
    'project_github_repository' => [
        'owner' => 'zendframework',
        'name'  => 'modules.zendframework.com',
    ],
    'router' => [
        'routes' => [
            'live-search' => [
                'type'    => 'Literal',
                'options' => [
                    'route'    => '/live-search',
                    'defaults' => [
                        'controller' => Controller\SearchController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'home' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action'     => 'index',
                    ],
                ],
                'priority' => 1,
                'may_terminate' => true,
            ],
            'contributors' => [
                'type' => 'Literal',
                'options' => [
                    'route'    => '/contributors',
                    'defaults' => [
                        'controller' => Controller\ContributorsController::class,
                        'action'     => 'index',
                    ],
                ],
            ],
            'feed' => [
                'type' => 'Literal',
                'options' => [
                    'route' => '/feed',
                    'defaults' => [
                        'controller' => Controller\IndexController::class,
                        'action' => 'feed',
                    ],
                ],
            ],
        ],
    ],
    'controllers' => [
        'factories' => [
            Controller\IndexController::class => Controller\IndexControllerFactory::class,
            Controller\ContributorsController::class => Controller\ContributorsControllerFactory::class,
            Controller\SearchController::class => Controller\SearchControllerFactory::class,
        ],
    ],
    'service_manager' => [
        'factories' => [
            \HTMLPurifier::class => Service\HtmlPurifierFactory::class,
            Log\LoggerInterface::class => Service\LoggerFactory::class,
            'project_github_repository' => Service\GitHubRepositoryFactory::class,
            Service\ErrorHandlingService::class => Service\ErrorHandlingServiceFactory::class,
            Service\RepositoryRetriever::class => Service\RepositoryRetrieverFactory::class,
        ],
    ],
    'view_manager' => [
        'display_not_found_reason' => false,
        'display_exceptions'       => false,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => [
            'application/index/index'           => __DIR__ . '/../view/application/index/index.phtml',
            'application/index/pagination'      => __DIR__ . '/../view/application/index/pagination.phtml',
            'application/search/index'          => __DIR__ . '/../view/application/search/index.phtml',
            'layout/error'                      => __DIR__ . '/../view/layout/layout.phtml',
            'layout/layout'                     => __DIR__ . '/../view/layout/layout.phtml',
            'error/404'                         => __DIR__ . '/../view/error/404.phtml',
            'error/index'                       => __DIR__ . '/../view/error/index.phtml',
        ],
        'template_path_stack' => [
            __DIR__ . '/../view',
        ],
        'strategies' => [
            'ViewFeedStrategy',
        ],
    ],
    'view_helpers' => [
        'invokables' => [
            'FlashMessenger'	=> View\Helper\FlashMessenger::class,
        ],
        'factories' => [
            'gitHubRepositoryUrl' => View\Helper\GitHubRepositoryUrlFactory::class,
            'sanitizeHtml' => View\Helper\SanitizeHtmlFactory::class,
        ],
    ],
];
