<?php

return [
    'modules' => [
        'ZF\DevelopmentMode',
        'ZfcBase',
        'ZfcUser',
        'ScnSocialAuth',
        'EdpGithub',
        'Application',
        'User',
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
