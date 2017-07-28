<?php

declare(strict_types=1);

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
use AltThree\Storage\Compressors\ZlibCompressor;
use AltThree\Storage\StorageFactory;
use AltThree\Storage\StorageManager;
use AltThree\Storage\StorageServiceProvider;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\Flysystem\FlysystemServiceProvider;
use GrahamCampbell\TestBench\AbstractPackageTestCase;
use GrahamCampbell\TestBenchCore\ServiceProviderTrait;

/**
 * This is the service provider test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ServiceProviderTest extends AbstractPackageTestCase
{
    use ServiceProviderTrait;

    protected function getRequiredServiceProviders($app)
    {
        return [
            FlysystemServiceProvider::class,
        ];
    }

    protected function getServiceProviderClass($app)
    {
        return StorageServiceProvider::class;
    }

    public function testCompressorIsInjectable()
    {
        $this->assertIsInjectable(CompressorInterface::class);
        $this->assertIsInjectable(ZlibCompressor::class);
    }

    public function testStorageFactoryIsInjectable()
    {
        $this->assertIsInjectable(StorageFactory::class);
    }

    public function testStorageManagerIsInjectable()
    {
        $this->assertIsInjectable(StorageManager::class);
    }

    public function testBindings()
    {
        $this->assertIsInjectable(StoreInterface::class);

        $original = $this->app['storage.connection'];
        $this->app['storage']->reconnect();
        $new = $this->app['storage.connection'];

        $this->assertNotSame($original, $new);
        $this->assertEquals($original, $new);
    }
}
