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

namespace AltThree\Tests\Storage\Stores;

use AltThree\Storage\Stores\FallbackStore;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\TestBench\AbstractTestCase;
use Mockery;

/**
 * This is the fallback store test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class FallbackStoreTest extends AbstractTestCase
{
    public function testAll()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('all')->once()->andReturn(['123']);
        $fallback->shouldReceive('all')->once()->andReturn(['456']);

        $this->assertSame(['123', '456'], $store->all());
    }

    public function testAllDupe()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('all')->once()->andReturn(['123']);
        $fallback->shouldReceive('all')->once()->andReturn(['123', '456']);

        $this->assertSame(['123', '456'], $store->all());
    }

    public function testGetData()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('get')->once()->with('foo')->andReturn('data');

        $this->assertSame('data', $store->get('foo'));
    }

    public function testGetFallback()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('get')->once()->with('foo');
        $fallback->shouldReceive('get')->once()->with('foo')->andReturn('hi');

        $this->assertSame('hi', $store->get('foo'));
    }

    public function testGetEmpty()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('get')->once()->with('baz');
        $fallback->shouldReceive('get')->once()->with('baz');

        $this->assertNull($store->get('baz'));
    }

    public function testPut()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('put')->once()->with('bar', 'data');

        $this->assertNull($store->put('bar', 'data'));
    }

    public function testDelete()
    {
        $main = Mockery::mock(StoreInterface::class);
        $fallback = Mockery::mock(StoreInterface::class);
        $store = new FallbackStore($main, $fallback);

        $main->shouldReceive('delete')->once()->with('bar');
        $fallback->shouldReceive('delete')->once()->with('bar');

        $this->assertNull($store->delete('bar'));
    }
}
