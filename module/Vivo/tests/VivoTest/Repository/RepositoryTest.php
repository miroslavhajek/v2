<?php
namespace VivoTest\Repository;

use Vivo\Repository\Repository;
use Vivo\Repository\Watcher;
use Vivo\Indexer\Indexer;
use Vivo\Storage\StorageInterface;
use Vivo\Repository\UuidConvertor\UuidConvertorInterface;
use Vivo\Storage\PathBuilder\PathBuilderInterface;
use Vivo\Uuid\GeneratorInterface as UuidGenerator;
use Vivo\IO\IOUtil;
use Vivo\IO\InputStreamInterface;
use Vivo\IO\OutputStreamInterface;
use Vivo\CMS\Model\Entity;
use VivoTest\SharedTestClasses\FsCacheMock;

use Zend\Serializer\Adapter\AdapterInterface as Serializer;
use Zend\Cache\Storage\Capabilities;

/**
 * RepositoryTest
 */
class RepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Repository
     */
    protected $repository;

    /**
     * @var FsCacheMock
     */
    protected $cache;

    /**
     * @var Watcher
     */
    protected $watcher;

    /**
     * @var Indexer
     */
    protected $indexer;

    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var UuidConvertorInterface
     */
    protected $uuidConvertor;

    /**
     * @var PathBuilderInterface
     */
    protected $pathBuilder;

    /**
     * @var UuidGenerator
     */
    protected $uuidGenerator;

    /**
     * @var Capabilities
     */
    protected $cacheCaps;

    /**
     * @var IOUtil
     */
    protected $ioUtil;

    /**
     * @var Entity
     */
    protected $entity;

    /**
     * @var InputStreamInterface
     */
    protected $inputStream;

    /**
     * @var OutputStreamInterface
     */
    protected $outputStream;

    public function setUp()
    {
        $mockedMethodsCache = array('setItem', 'hasItem', 'removeItem', 'addItem', 'getCapabilities');
        $this->cache        = $this->getMock('VivoTest\SharedTestClasses\FsCacheMock',
            $mockedMethodsCache, array(), '', false);


        $this->storage          = $this->getMock('Vivo\Storage\StorageInterface', array(), array(), '', false);
        $this->watcher          = $this->getMock('Vivo\Repository\Watcher', array(), array(), '', false);
        $this->indexer          = $this->getMock('Vivo\Indexer\Indexer', array(), array(), '', false);
        $this->uuidGenerator    = $this->getMock('Vivo\Uuid\GeneratorInterface', array(), array(), '', false);
        $this->ioUtil           = $this->getMock('Vivo\IO\IOUtil', array(), array(), '', false);
        $this->pathBuilder      = $this->getMock(
                                    'Vivo\Storage\PathBuilder\PathBuilderInterface', array(), array(), '', false);
        $this->serializer       = $this->getMock(
                                    'Zend\Serializer\Adapter\AdapterInterface', array(), array(), '', false);
        $this->uuidConvertor    = $this->getMock(
                                    'Vivo\Repository\UuidConvertor\UuidConvertorInterface',
                                    array(), array(), '', false);
        $this->cacheCaps        = $this->getMock('Zend\Cache\Storage\Capabilities', array(), array(), '', false);
        $this->entity           = $this->getMock('Vivo\CMS\Model\Entity', array(), array(), '', false);
        $this->inputStream      = $this->getMock('Vivo\IO\InputStreamInterface', array(), array(), '', false);
        $this->outputStream     = $this->getMock('Vivo\IO\OutputStreamInterface', array(), array(), '', false);
        $cacheSupportedTypes    = array(
            'NULL'     => true,
            'boolean'  => true,
            'integer'  => true,
            'double'   => true,
            'string'   => true,
            'array'    => true,
            'object'   => true,
            'resource' => false,
        );
        $this->cacheCaps->expects($this->any())
            ->method('getSupportedDataTypes')
            ->will($this->returnValue($cacheSupportedTypes));
        $this->cache->expects($this->any())
            ->method('getCapabilities')
            ->will($this->returnValue($this->cacheCaps));

        $this->repository       = new Repository($this->storage,
                                                 $this->cache,
                                                 $this->indexer,
                                                 $this->serializer,
                                                 $this->uuidConvertor,
                                                 $this->watcher,
                                                 $this->pathBuilder,
                                                 $this->uuidGenerator,
                                                 $this->ioUtil);
    }

    public function testGetEntityReturnsNullForNonExistent()
    {
        $ident          = '012345678901234567890123456789AB';
        $path           = '/abc/def/ghi';
        $pathElements   = array($path, Repository::ENTITY_FILENAME);
        $pathToEntityFile   = $path . '/'. Repository::ENTITY_FILENAME;
        $this->watcher->expects($this->once())
            ->method('get')
            ->with($this->equalTo($ident))
            ->will($this->returnValue(null));
        $this->cache->setData(null);
        $this->cache->setSuccess(false);
        $this->uuidConvertor->expects($this->once())
            ->method('getPathFromUuid')
            ->with($this->equalTo($ident))
            ->will($this->returnValue($path));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->with($this->equalTo($pathElements), $this->equalTo(true))
            ->will($this->returnValue($pathToEntityFile));
        $this->storage->expects($this->once())
            ->method('isObject')
            ->with($pathToEntityFile)
            ->will($this->returnValue(false));
        $this->storage->expects($this->never())
            ->method('get');
        $entity = $this->repository->getEntity($ident);
        $this->assertNull($entity);
    }

    public function testGetEntityWatcherHit()
    {
        $ident          = '012345678901234567890123456789AB';
        $this->watcher->expects($this->once())
            ->method('get')
            ->with($this->equalTo($ident))
            ->will($this->returnValue($this->entity));
        $this->storage->expects($this->never())
            ->method('isObject');
        $this->storage->expects($this->never())
            ->method('get');
        $entity = $this->repository->getEntity($ident);
        $this->assertSame($this->entity, $entity);
    }

    public function testGetEntityCacheHit()
    {
        $ident          = '012345678901234567890123456789AB';
        $this->watcher->expects($this->once())
            ->method('get')
            ->with($this->equalTo($ident))
            ->will($this->returnValue(null));
        $this->watcher->expects($this->once())
            ->method('add')
            ->with($this->equalTo($this->entity));
        $this->cache->setData($this->entity);
        $this->cache->setSuccess(true);
        $this->storage->expects($this->never())
            ->method('isObject');
        $this->storage->expects($this->never())
            ->method('get');
        $this->assertSame($this->entity, $this->repository->getEntity($ident));
    }

    public function testGetEntityStorageHit()
    {
        $ident          = '012345678901234567890123456789AB';
        $uuid           = $ident;
        $path           = '/abc/def/ghi';
        $pathElements   = array($path, Repository::ENTITY_FILENAME);
        $pathToEntityFile   = $path . '/'. Repository::ENTITY_FILENAME;
        $serializedEntity   = 'xyz123';
        $this->watcher->expects($this->once())
            ->method('get')
            ->with($this->equalTo($ident))
            ->will($this->returnValue(null));
        $this->watcher->expects($this->once())
            ->method('add')
            ->with($this->equalTo($this->entity));
        $this->cache->setData(null);
        $this->cache->setSuccess(false);
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($uuid), $this->equalTo($this->entity));
        $this->uuidConvertor->expects($this->once())
            ->method('getPathFromUuid')
            ->with($this->equalTo($ident))
            ->will($this->returnValue($path));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->with($this->equalTo($pathElements), $this->equalTo(true))
            ->will($this->returnValue($pathToEntityFile));
        $this->storage->expects($this->once())
            ->method('isObject')
            ->with($pathToEntityFile)
            ->will($this->returnValue(true));
        $this->storage->expects($this->once())
            ->method('get')
            ->with($this->equalTo($pathToEntityFile))
            ->will($this->returnValue($serializedEntity));
        $this->serializer->expects($this->once())
            ->method('unserialize')
            ->with($this->equalTo($serializedEntity))
            ->will($this->returnValue($this->entity));
        $entity = $this->repository->getEntity($ident);
        $this->assertSame($this->entity, $entity);
    }

    public function testGetEntityStorageHitByPath()
    {
        $ident          = '/abc/def/ghi';
        $uuid           = '012345678901234567890123456789AB';
        $path           = '/abc/def/ghi';
        $pathElements   = array($path, Repository::ENTITY_FILENAME);
        $pathToEntityFile   = $path . '/'. Repository::ENTITY_FILENAME;
        $serializedEntity   = 'xyz123';
        $this->watcher->expects($this->once())
            ->method('get')
            ->with($this->equalTo($uuid))
            ->will($this->returnValue(null));
        $this->watcher->expects($this->once())
            ->method('add')
            ->with($this->equalTo($this->entity));
        $this->cache->setData(null);
        $this->cache->setSuccess(false);
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($uuid), $this->equalTo($this->entity));
        $this->uuidConvertor->expects($this->once())
            ->method('getUuidFromPath')
            ->with($this->equalTo($ident))
            ->will($this->returnValue($uuid));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->with($this->equalTo($pathElements), $this->equalTo(true))
            ->will($this->returnValue($pathToEntityFile));
        $this->storage->expects($this->once())
            ->method('isObject')
            ->with($pathToEntityFile)
            ->will($this->returnValue(true));
        $this->storage->expects($this->once())
            ->method('get')
            ->with($this->equalTo($pathToEntityFile))
            ->will($this->returnValue($serializedEntity));
        $this->serializer->expects($this->once())
            ->method('unserialize')
            ->with($this->equalTo($serializedEntity))
            ->will($this->returnValue($this->entity));
        $entity = $this->repository->getEntity($ident);
        $this->assertSame($this->entity, $entity);
    }

    public function testDeleteEntity()
    {
        $path       = '/abc/def/ghi';
        $uuid       = '012345678901234567890123456789AB';
        $tmpDelPath = '/del-0123456789';
        $this->entity->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($path));
        $this->entity->expects($this->any())
            ->method('getUuid')
            ->will($this->returnValue($uuid));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->will($this->returnValue($tmpDelPath));
        $this->storage->expects($this->once())
            ->method('move')
            ->with($this->equalTo($path), $this->equalTo($tmpDelPath));
        $this->storage->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($tmpDelPath))
            ->will($this->returnValue(true));
        $this->storage->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($tmpDelPath));
        $this->watcher->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($uuid));
        $this->cache->expects($this->once())
            ->method('removeItem')
            ->with($this->equalTo($uuid));
        //TODO - better assertion for indexer
        $this->indexer->expects($this->once())
            ->method('execute');
        $this->repository->deleteEntity($this->entity);
        $this->repository->commit();
    }

    public function testDeleteResource()
    {
        $path       = '/abc/def/ghi';
        $rscName    = 'myResource';
        $rscPath    = $path . '/' . $rscName;
        $tmpDelPath = '/del-0123456789';
        $this->entity->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($path));
        $this->pathBuilder->expects($this->exactly(2))
            ->method('buildStoragePath')
            ->will($this->onConsecutiveCalls($rscPath, $tmpDelPath));
        $this->storage->expects($this->once())
            ->method('move')
            ->with($this->equalTo($rscPath), $this->equalTo($tmpDelPath));
        $this->storage->expects($this->once())
            ->method('contains')
            ->with($this->equalTo($tmpDelPath))
            ->will($this->returnValue(true));
        $this->storage->expects($this->once())
            ->method('remove')
            ->with($this->equalTo($tmpDelPath));
        $this->repository->deleteResource($this->entity, $rscName);
        $this->repository->commit();
    }

    public function testSaveEntity()
    {
        $uuid   = '012345678901234567890123456789AB';
        $path   = '/abc/def/ghi';
        $entityPath = $path . '/' . Repository::ENTITY_FILENAME;
        $entitySer  = 'abc123';
        $this->entity->expects($this->any())
            ->method('getUuid')
            ->will($this->returnValue($uuid));
        $this->entity->expects($this->any())
            ->method('getPath')
            ->will($this->returnValue($path));
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->will($this->returnValue($entityPath));
        $this->serializer->expects($this->once())
            ->method('serialize')
            ->with($this->entity)
            ->will($this->returnValue($entitySer));
        $this->storage->expects($this->once())
            ->method('set')
            //We do not know the temp path, as it is generated inside the SUT, therefor $this->anything()
            ->with($this->anything(), $this->equalTo($entitySer));
        $this->storage->expects($this->once())
            ->method('move')
            //We do not know the temp path, as it is generated inside the SUT, therefor $this->anything()
            ->with($this->anything(), $this->equalTo($entityPath))
            ->will($this->returnValue(true));
        $this->watcher->expects($this->once())
            ->method('add')
            ->with($this->equalTo($this->entity));
        $this->cache->expects($this->once())
            ->method('setItem')
            ->with($this->equalTo($uuid), $this->equalTo($this->entity));
        $this->indexer->expects($this->once())
            ->method('save')
            ->with($this->entity);
        $this->repository->saveEntity($this->entity);
        $this->repository->commit();
    }

    public function testSaveResource()
    {
        $data       = 'abcdefghijkl123546879';
        $path       = '/abc/def/ghi';
        $rscName    = 'myResource';
        $rscPath    = $path . '/' . $rscName;
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->will($this->returnValue($rscPath));
        $this->storage->expects($this->once())
            ->method('set')
            ->with($this->anything(), $this->equalTo($data));
        $this->storage->expects($this->once())
            ->method('move')
            ->with($this->anything(), $this->equalTo($rscPath))
            ->will($this->returnValue(true));
        $this->repository->saveResource($this->entity, $rscName, $data);
        $this->repository->commit();
    }

    public function testWriteResource()
    {
        $path       = '/abc/def/ghi';
        $rscName    = 'myResource';
        $rscPath    = $path . '/' . $rscName;
        $this->pathBuilder->expects($this->once())
            ->method('buildStoragePath')
            ->will($this->returnValue($rscPath));
        $this->storage->expects($this->once())
            ->method('write')
            ->will($this->returnValue($this->outputStream));
        $this->ioUtil->expects($this->once())
            ->method('copy')
            ->with($this->inputStream, $this->outputStream, $this->anything());
        $this->storage->expects($this->once())
            ->method('move')
            ->with($this->anything(), $this->equalTo($rscPath))
            ->will($this->returnValue(true));
        $this->repository->writeResource($this->entity, $rscName, $this->inputStream);
        $this->repository->commit();
    }
}
