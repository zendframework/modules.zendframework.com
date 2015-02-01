<?php
/**
 * ScnSocialAuth Configuration
 *
 * If you have a ./config/autoload/ directory set up for your project, you can
 * drop this config file in it and change the values as you wish.
 */
$settings = [
    /**
     * Github Enabled
     *
     * Please specify if Github is enabled
     *
     * You can register a new application at:
     * https://github.com/settings/applications/new
     */
    'github_enabled' => true,
    'github_scope' => 'user:email,read:org',

];

/**
 * You do not need to edit below this line
 */
return [
    'scn-social-auth' => $settings,
    'service_manager' => [
        'aliases' => [
            'ScnSocialAuth_ZendDbAdapter' => (isset($settings['zend_db_adapter'])) ? $settings['zend_db_adapter'] : 'Zend\Db\Adapter\Adapter',
            'ScnSocialAuth_ZendSessionManager' => (isset($settings['zend_session_manager'])) ? $settings['zend_session_manager'] : 'Zend\Session\SessionManager',
        ],
    ],
];
