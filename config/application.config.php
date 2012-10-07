<?php
return array(
    'modules' => array(
        'ZfcBase',
        'ZfcUser',
        'ScnSocialAuth',
        'Application',
        'AssetManager',
        'Assetic',
        'HybridAuth',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            'ScnSocialAuth' => '../moduledev/ScnSocialAuth',
            './module',
            './vendor',
            '../moduledev',
        ),
    ),
);
