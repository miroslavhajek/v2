<?php
//Global configuration of caching
return array(
    //Configuration of caches used for individual cache subjects
    //To turn cache off, set it to null: 'cache' => null
    'cache' => array(
        'repository'                    => 'repository_fs',
        'navigation'                    => 'navigation_fs',
        'overview'                      => 'overview_fs',
        'translit_path'                 => null,
        'translit_url'                  => null,
        'translit_doc_title_to_path'    => null,
        'translit_mb_string_compare'    => null,
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