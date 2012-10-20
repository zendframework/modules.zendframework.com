<?php
return array(
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'UserRepositories' => 'User\View\Helper\UserRepositories'
        ),
    ),
);