<?php

/*
 * This file is part of Alt Three Storage.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Tests\Storage;

use AltThree\Storage\Compressors\CompressorInterface;
use AltThree\Storage\StorageFactory;
use AltThree\Storage\Stores\CachingStore;
use AltThree\Storage\Stores\CompressingStore;
use AltThree\Storage\Stores\EncryptingStore;
use AltThree\Storage\Stores\FallbackStore;
use AltThree\Storage\Stores\FlysystemStore;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\Flysystem\FlysystemManager;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Contracts\Encryption\Encrypter;
use League\Flysystem\FilesystemInterface;
use Mockery;

/**
 * This is the storage factory test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class StorageFactoryTest extends AbstractTestCase
{
    public function testMakeFlysystemStore()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $return = $factory->make(['main' => 'local', 'fallback' => null, 'cache' => false, 'encryption' => false, 'compression' => false]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(FlysystemStore::class, $return);
    }

    public function testMakeFallbackStore()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $flysystem->shouldReceive('connection')->once()->with('null')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $return = $factory->make(['main' => 'local', 'fallback' => 'null', 'cache' => false, 'encryption' => false, 'compression' => false]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(FallbackStore::class, $return);
    }

    public function testMakeCachingStore()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $cache->shouldReceive('store')->once()->with('redis')
            ->andReturn(Mockery::mock(Repository::class));

        $return = $factory->make(['main' => 'local', 'fallback' => null, 'cache' => 'redis', 'encryption' => false, 'compression' => false]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(CachingStore::class, $return);
    }

    public function testMakeEncryptingStore()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $return = $factory->make(['main' => 'local', 'fallback' => null, 'cache' => false, 'encryption' => true, 'compression' => false]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(EncryptingStore::class, $return);
    }

    public function testMakeCompressingStore()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $return = $factory->make(['main' => 'local', 'fallback' => null, 'cache' => false, 'encryption' => false, 'compression' => true]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(CompressingStore::class, $return);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Compression cannot be enabled at the same time as encryption.
     */
    public function testMakeCompressingWithEncrypting()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $return = $factory->make(['main' => 'local', 'fallback' => null, 'cache' => false, 'encryption' => true, 'compression' => true]);
    }

    public function testMakeEverything()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $cache->shouldReceive('store')->once()->with('array')
            ->andReturn(Mockery::mock(Repository::class));

        $return = $factory->make(['main' => 'local', 'fallback' => null, 'cache' => 'array', 'encryption' => true, 'compression' => false]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(CachingStore::class, $return);
    }

    public function testMakeEverythingAndFallback()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $flysystem->shouldReceive('connection')->once()->with('local')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $flysystem->shouldReceive('connection')->once()->with('null')
            ->andReturn(Mockery::mock(FilesystemInterface::class));

        $cache->shouldReceive('store')->once()->with('array')
            ->andReturn(Mockery::mock(Repository::class));

        $return = $factory->make(['main' => 'local', 'fallback' => 'null', 'cache' => 'array', 'encryption' => false, 'compression' => true]);

        $this->assertInstanceOf(StoreInterface::class, $return);
        $this->assertInstanceOf(CachingStore::class, $return);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The store requires flysystem configuration.
     */
    public function testMakeWithoutMain()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $factory->make(['fallback' => 'local', 'cache' => 'foo', 'encryption' => true, 'compression' => true]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The store requires flysystem configuration.
     */
    public function testMakeWithoutFallback()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $factory->make(['main' => 'local', 'cache' => 'foo', 'encryption' => true, 'compression' => true]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The store requires cache configuration.
     */
    public function testMakeWithoutCache()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $factory->make(['main' => 'bar', 'fallback' => null, 'encryption' => false, 'compression' => false]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The store requires encryption configuration.
     */
    public function testMakeWithoutEncryption()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $factory->make(['main' => 'baz', 'fallback' => null, 'cache' => false, 'compression' => true]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The store requires compression configuration.
     */
    public function testMakeWithoutCompression()
    {
        $cache = Mockery::mock(Factory::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $flysystem = Mockery::mock(FlysystemManager::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $factory = new StorageFactory($cache, $encrypter, $flysystem, $compressor);

        $factory->make(['main' => 'baz', 'fallback' => null, 'cache' => false,  'encryption' => false]);
    }
}
