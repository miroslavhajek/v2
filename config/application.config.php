<?php
return array(
    'modules' => array(
        'DluPhpSettings',
        'Vivo',
        'ZF2NetteDebug',
        'ZendSearch',
        'ApacheSolr',
        'DluTwBootstrap',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
            //TODO - Remove for production!
//            'c:\Projects\LDG\Vivo2\zfmodules',
//            'Vivo'  => 'c:\Projects\LDG\Vivo2\zfmodules\vivoportal',
        ),
    ),
);
