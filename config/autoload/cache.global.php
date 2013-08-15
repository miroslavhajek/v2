<?php
//Global configuration of caching
return array(
    //Repository configuration - 'cache' items refer to the names of caches defined in cache_manager (see below)
    //To turn cache off, set it to null: 'cache' => null
    'repository'    => array(
        //Repository cache - enable Filesystem cache by default
        'cache'         => 'repository_fs',
    ),
    'cms'       => array(
        'ui'        => array(
            'Vivo\UI\Content\Navigation'    => array(
                //Cache for navigation containers - enable Filesystem cache by default
                'cache'     => 'navigation_fs',
            ),
            'Vivo\UI\Content\Overview'      => array(
                //Cache for overview pages - enable Filesystem cache by default
                'cache'     => 'overview_fs',
            ),
        ),
    ),
    //Front controller configuration
    'front_controller'  => array(
        //Disable the output cache by setting to null
        'output_cache'      => null,
        //'output_cache'      => 'output_fs',
    ),
    //Transliterator caching seems to slow down the processing instead of a boost! - so turn the caches off!
    'transliterator'    => array(
        'path'              => array(
            'cache'             => null,
            //'cache'             => 'translit_path_fs',
        ),
        'url'              => array(
            'cache'             => null,
            //'cache'             => 'translit_url_fs',
        ),
        'doc_title_to_path' => array(
            'cache'             => null,
            //'cache'             => 'translit_doctitletopath_fs',
        ),
        'mb_string_compare' => array(
            'cache'             => null,
            //'cache'             => 'translit_mbstringcompare_fs',
        ),
    ),

    //Cache manager configuration - define caches here
    'cache_manager'         => array(
        //Cache for repository objects - storage: file system
        'repository_fs'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/repository',
                    'namespace' => 'repository',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for repository objects - storage: ZendServer - Disk
        'repository_zsdisk'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'ZendServerDisk',
                'options'   => array(
                    'namespace' => 'repository',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for repository objects - storage: ZendServer - Shared memory
        'repository_zsshm'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'ZendServerShm',
                'options'   => array(
                    'namespace' => 'repository',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for navigation containers - storage: Filesystem
        'navigation_fs'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/navigation',
                    'namespace' => 'navigation',
                    'ttl'       => 3600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for navigation containers - storage: ZendServer - Disk
        'navigation_zsdisk'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'ZendServerDisk',
                'options'   => array(
                    'namespace' => 'navigation',
                    'ttl'       => 3600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for navigation containers - storage: ZendServer - Shared memory
        'navigation_zsshm'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'ZendServerShm',
                'options'   => array(
                    'namespace' => 'navigation',
                    'ttl'       => 3600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for overview documents - storage: Filesystem
        'overview_fs'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/overview',
                    'namespace' => 'overview',
                    'ttl'       => 3600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for overview documents - storage: ZendServer - Disk
        'overview_zsdisk'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'ZendServerDisk',
                'options'   => array(
                    'namespace' => 'overview',
                    'ttl'       => 3600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Cache for overview documents - storage: ZendServer - Shared memory
        'overview_zsshm'     => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'ZendServerShm',
                'options'   => array(
                    'namespace' => 'overview',
                    'ttl'       => 3600,
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),

//        //Cache for document output - storage: Filesystem
//        'output_fs'     => array(
//            //Options to pass to StorageFactory::factory()
//            'adapter'   => array(
//                'name'      => 'Filesystem',
//                'options'   => array(
//                    'cache_dir' => __DIR__ . '/../../data/cache/output',
//                    'namespace' => 'output',
//                ),
//            ),
//        ),

        //Transliterator caching seems to slow down the processing instead of a boost!
        //Transliterator cache for path transliterator
        'translit_path_fs'  => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/translit_path',
                    'namespace' => 'translit_path',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Transliterator cache for url transliterator
        'translit_url_fs'  => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/translit_url',
                    'namespace' => 'translit_url',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Transliterator cache for mbstringcompare transliterator
        'translit_mbstringcompare_fs'  => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/translit_mbstringcompare',
                    'namespace' => 'translit_mbstringcompare',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
        //Transliterator cache for doctitletopath transliterator
        'translit_doctitletopath_fs'  => array(
            //Options to pass to StorageFactory::factory()
            'adapter'   => array(
                'name'      => 'Filesystem',
                'options'   => array(
                    'cache_dir' => __DIR__ . '/../../data/cache/translit_doctitletopath',
                    'namespace' => 'translit_doctitletopath',
                ),
            ),
            'plugins'   => array(
                'serializer'    => array(),
            ),
        ),
    ),
);