<?php

/*
 * This file is part of Alt Three Storage.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Tests\Storage\Stores;

use AltThree\Storage\Stores\FlysystemStore;
use GrahamCampbell\TestBench\AbstractTestCase;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use Mockery;

/**
 * This is the flysystem store test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class FlysystemStoreTest extends AbstractTestCase
{
    public function testAll()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('listContents')->once()->andReturn([['path' => '123']]);

        $this->assertSame(['123'], $store->all());
    }

    public function testGetData()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('read')->once()->with('foo')->andReturn('data');

        $this->assertSame('data', $store->get('foo'));
    }

    public function testGetNull()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('read')->once()->with('bar')->andReturn(null);

        $this->assertNull($store->get('bar'));
    }

    public function testGetEmpty()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('read')->once()->with('bar')->andReturn('');

        $this->assertNull($store->get('bar'));
    }

    public function testGetNotFound()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('read')->once()->with('baz')->andThrow(FileNotFoundException::class);

        $this->assertNull($store->get('baz'));
    }

    public function testPut()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('put')->once()->with('bar', 'data');

        $this->assertNull($store->put('bar', 'data'));
    }

    public function testDelete()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('delete')->once()->with('bar');

        $this->assertNull($store->delete('bar'));
    }

    public function testDeleteFail()
    {
        $flysystem = Mockery::mock(FilesystemInterface::class);
        $store = new FlysystemStore($flysystem);

        $flysystem->shouldReceive('delete')->once()->with('bar')->andThrow(FileNotFoundException::class);

        $this->assertNull($store->delete('bar'));
    }
}
