<?php
return array(
    'modules' => array(
        'VpLogger',
        'ZF2NetteDebug',
        'DluPhpSettings',
        'Vp',
        'Vivo',
        'VpApacheSolr',
        'DluTwBootstrap',
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
