<?php
return array(
    'modules' => array(
        'DluPhpSettings',
        'Vp',
        'Vivo',
        'ZF2NetteDebug',
//        'ZendSearch',
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
