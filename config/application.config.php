<?php
ini_set('display_errors', 1);

return [
    'modules' => [
        'ZF\DevelopmentMode',
        'AssetManager',
        'ZfcBase',
        'ZfcUser',
        'ScnSocialAuth',
        'EdpGithub',
        'Application',
        'User',
        'EdpModuleLayouts',
        'ZfModule',
    ],
    'module_listener_options' => [
        'config_glob_paths'    => [
            'config/autoload/{,*.}{global,local}.php',
        ],
        'module_paths' => [
            './module',
            './vendor',
        ],
    ],
];
