<?php
ini_set('display_errors', 1);
require(__DIR__.'/constants.php');

return array(
    'modules' => array(
        'LfjErrorLayout',
        'ZfcBase',
        'ZfcUser',
        'ScnSocialAuth',
        'EdpGithub',
        'Application',
        'AssetManager',
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
