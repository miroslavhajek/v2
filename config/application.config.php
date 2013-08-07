<?php
return array(
    'modules' => array(
        'DluPhpSettings',
        'VpLogger',
        'ZF2NetteDebug',
        'Vp',
        'Vivo',
        'VpApacheSolr',
        'DluTwBootstrap',
        //TODO - remove!
        'VivoDevel',
    ),
    'module_listener_options' => array(
        'config_glob_paths'    => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
            //TODO - remove!
            'c:\Projects\LDG\Vivo2\zfmodules',
            'Vivo'  => 'c:\Projects\LDG\Vivo2\zfmodules\vivoportal',
        ),
    ),
);
