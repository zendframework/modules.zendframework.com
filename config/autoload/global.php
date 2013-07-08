<?php
return array(
    'module_layouts' => array(
        'ZfcUser' => 'layout/layout-small-header.phtml',
        'ZfModule' => 'layout/layout-small-header.phtml',
    ),
    'asset_manager' => array(
        'caching' => array(
            'default' => array(
                'cache'     => 'FilePath',  // Apc, FilePath, FileSystem etc.
                'options' => array(
                    'dir' => 'public',
                ),
            ),
        ),
    ),
);
