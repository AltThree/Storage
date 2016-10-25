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

use AltThree\Storage\Compressors\CompressorInterface;
use AltThree\Storage\Stores\CompressingStore;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\TestBench\AbstractTestCase;
use Mockery;

/**
 * This is the compressing store test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CompressingStoreTest extends AbstractTestCase
{
    public function testAll()
    {
        $store = Mockery::mock(StoreInterface::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $compressing = new CompressingStore($store, $compressor);

        $store->shouldReceive('all')->andReturn(['123']);

        $this->assertSame(['123'], $compressing->all());
    }

    public function testGetData()
    {
        $store = Mockery::mock(StoreInterface::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $compressing = new CompressingStore($store, $compressor);

        $store->shouldReceive('get')->once()->with('foo')->andReturn('raw');
        $compressor->shouldReceive('uncompress')->once()->with('raw')->andReturn('data');

        $this->assertSame('data', $compressing->get('foo'));
    }

    public function testGetEmpty()
    {
        $store = Mockery::mock(StoreInterface::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $compressing = new CompressingStore($store, $compressor);

        $store->shouldReceive('get')->once()->with('baz');

        $this->assertNull($compressing->get('baz'));
    }

    public function testPut()
    {
        $store = Mockery::mock(StoreInterface::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $compressing = new CompressingStore($store, $compressor);

        $compressor->shouldReceive('compress')->once()->with('data')->andReturn('raw');
        $store->shouldReceive('put')->once()->with('bar', 'raw');

        $this->assertNull($compressing->put('bar', 'data'));
    }

    public function testDelete()
    {
        $store = Mockery::mock(StoreInterface::class);
        $compressor = Mockery::mock(CompressorInterface::class);
        $compressing = new CompressingStore($store, $compressor);

        $store->shouldReceive('delete')->once()->with('bar');

        $this->assertNull($compressing->delete('bar'));
    }
}
