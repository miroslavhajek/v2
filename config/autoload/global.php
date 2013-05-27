<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overridding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will be typically INCLUDED in your source
 * control, so do NOT include passwords or other sensitive information in this
 * file.
 */

return array(
    'phpSettings'   => array(
        'display_startup_errors'        => false,
        'display_errors'                => false,
        'error_reporting'               => E_ALL | E_STRICT,
//        'max_execution_time'		    => 60,
        'date.timezone'                 => 'Europe/Prague',
        'mbstring.internal_encoding'    => 'UTF-8',
    ),
    'logger' => array(
        'writers' => array (
            //writers from writer plugin manager
            'default_log'   => array(
                'priority'  => 1,
                'options'   => array(
                    'log_dir'   => __DIR__ . '/../../data/logs',
                ),
            ),
        ),
    ),
    //Vivo Modules configuration
    'modules'   => array(
        'storage'   => array(
            'class'     => 'Vivo\Storage\LocalFileSystemStorage',
            'options'   => array(
                'root'          => __DIR__ . '/../../vmodule',
            ),
        ),
    ),
    //Cache manager configuration - define in global/local config
    'cache_manager'         => array(
        //Cache for repository objects - storage: file system
        'repository_fs'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/repository',
                    'namespace' => 'repo',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for navigation containers - storage: file system
        'navigation'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/navigation',
                    'namespace' => 'nav',
                    'ttl'       => 600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
    ),
    //Repository configuration
    'repository'    => array(
        //Storage for repository - configure in global/local config
        'storage'              => array(
            'class'     => 'Vivo\Storage\LocalFileSystemStorage',
            'options'   => array(
                'root'          => __DIR__ . '/../../data/repository',
            ),
        ),
        //Cache
        'cache'         => 'repository_fs',
    ),
);
