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
    'VpLogger\logger' => array(
        'writers' => array (
            //Writers from writer plugin manager
            'error_log'   => array(
                'enabled'   => true,
                'priority'  => 1,
                'options'   => array(
                    'log_dir'       => __DIR__ . '/../../data/logs',
                    'log_name'      => 'vivo_error',
                    'priority_min'  => null,
                    'priority_max'  => VpLogger\Log\Logger::NOTICE,
                    'events'        => array(
                        'allow'         => array(
                            //'all'           => array('*', '*'),
                        ),
                        'block'         => array(
                        ),
                    ),
                    //'format'        => null,
                ),
            ),
            'perf_log'      => array(
                'enabled'   => false,
                'enable_by_session' => false,
                'session'   => array(
                    'namespace' => 'VpLogger',
                    'var'       => 'perf_log',
                ),
                'priority'  => 2,
                'options'   => array(
                    'log_dir'   => __DIR__ . '/../../data/logs',
                    'log_name'      => 'vivo_perf',
//                    'priority_min'  => null, //VpLogger\Log\Logger::PERF_BASE,
//                    'priority_max'  => VpLogger\Log\Logger::PERF_FINEST,
                    'events'        => array(
                        'allow'         => array(
                            //'all'           => array('*', '*'),
//                            'all'       => null,
//                            'cache'     => array('Zend\Cache\Storage\Adapter\Filesystem', '*'),
                        ),
                        'block'         => array(
                        ),
                    ),
                    //'format'    => null,
                ),
            ),
            'firephp_log'      => array(
                'enabled'   => false,
                'enable_by_session' => false,
                'session'   => array(
                    'namespace' => 'VpLogger',
                    'var'       => 'firephp_log',
                ),
                'priority'  => 3,
                'options'   => array(
                    'priority_min'  => null, //VpLogger\Log\Logger::PERF_BASE,
                    'priority_max'  => VpLogger\Log\Logger::PERF_FINEST,
                    'events'        => array(
                        'allow'         => array(
                            //'all'           => array('*', '*'),
                            //'all'       => null,
                            //'cache'     => array('Zend\Cache\Storage\Adapter\Filesystem', 'removeItem.pre'),
                        ),
                        'block'         => array(
                            //'cache'     => array('Zend\Cache\Storage\Adapter\Filesystem', '*'),
                        ),
                    ),
                    //'format'    => null,
                ),
            ),
        ),
        'writer_plugin_manager' => array(
            'factories'     => array(
                'error_log'         => 'VpLogger\Log\DailyLogFileWriterFactory',
                'perf_log'          => 'VpLogger\Log\PerRequestLogFileWriterFactory',
                'firephp_log'       => 'VpLogger\Log\FirePhpWriterFactory',
            ),
        ),
    ),
    //Core setup
    'setup'     => array(
        'ports'     => array(
            'http'  => 80,
            'https' => 443,
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
        //Cache for navigation containers
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
        //Cache for overview documents
        'overview'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/overview',
                    'namespace' => 'overview',
                    'ttl'       => 600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
//        //Cache for document output
//        'output'     => array(
//            //Options to pass to StorageFactory::factory()
//            'adapter'   => array(
//                'name'      => 'filesystem',
//                'options'   => array(
//                    'cache_dir' => __DIR__ . '/../../data/cache/output',
//                    'namespace' => 'output',
//                ),
//            ),
//        ),
    ),
//    //Front controller configuration
//    'front_controller'  => array(
//        //Name of the cache used to cache the document output; set to null to disable
//        'output_cache'      => 'output',
//    ),
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
    'cms'       => array(
        'ui'        => array(
            'Vivo\UI\Content\Navigation'    => array(
                //Cache for navigation containers
                'cache'     => 'navigation',
            ),
            'Vivo\UI\Content\Overview'      => array(
                //Cache for overview pages
                'cache'     => 'overview',
            ),
        ),
    ),
);
