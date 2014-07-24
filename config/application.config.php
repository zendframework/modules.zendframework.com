<?php
ini_set('display_errors', 1);
require(__DIR__.'/constants.php');

return array(
    'modules' => array(
        'Application',
        'ZF\DevelopmentMode',
        'ZF\Apigility',
        'ZF\Apigility\Provider',
        'ZF\Apigility\Welcome',
        'ZF\Apigility\Documentation',
        'ZF\ApiProblem',
        'ZF\MvcAuth',
        'ZF\OAuth2',
        'ZF\Hal',
        'ZF\ContentNegotiation',
        'ZF\ContentValidation',
        'ZF\Rest',
        'ZF\Rpc',
        'ZF\Versioning',
        /* Old Modules may be replaced integrating Apigility */
        'AssetManager',
        'LfjErrorLayout',
        'ZfcBase',
        'ZfcUser',
        'ScnSocialAuth',
        'EdpGithub',
        'Application',
		'User',
        'EdpModuleLayouts',
        'ZfModule',
        'EdpMarkdown',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
    ),
);
