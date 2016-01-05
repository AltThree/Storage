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

use AltThree\Storage\Stores\CachingStore;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Cache\Repository;
use Illuminate\Contracts\Cache\Store;
use Mockery;

/**
 * This is the caching store test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CachingStoreTest extends AbstractTestCase
{
    public function testGetMissingCache()
    {
        $store = Mockery::mock(StoreInterface::class);
        $cache = Mockery::mock(Store::class);
        $caching = new CachingStore($store, new Repository($cache));

        $cache->shouldReceive('get')->once()->with('store.foo');
        $store->shouldReceive('get')->once()->with('foo')->andReturn('bar');
        $cache->shouldReceive('put')->once()->with('store.foo', 'bar', 30);

        $this->assertSame('bar', $caching->get('foo'));
    }

    public function testGetHittingCache()
    {
        $store = Mockery::mock(StoreInterface::class);
        $cache = Mockery::mock(Store::class);
        $caching = new CachingStore($store, new Repository($cache));

        $cache->shouldReceive('get')->once()->with('store.baz')->andReturn('WHY HERRO THERE!');

        $this->assertSame('WHY HERRO THERE!', $caching->get('baz'));
    }

    public function testGetEmpty()
    {
        $store = Mockery::mock(StoreInterface::class);
        $cache = Mockery::mock(Store::class);
        $caching = new CachingStore($store, new Repository($cache));

        $cache->shouldReceive('get')->once()->with('store.example');
        $store->shouldReceive('get')->once()->with('example');
        $cache->shouldReceive('put')->once()->with('store.example', null, 30);

        $this->assertNull($caching->get('example'));
    }

    public function testPut()
    {
        $store = Mockery::mock(StoreInterface::class);
        $cache = Mockery::mock(Store::class);
        $caching = new CachingStore($store, new Repository($cache));

        $cache->shouldReceive('put')->once()->with('store.name', 'stuff', 30);
        $store->shouldReceive('put')->once()->with('name', 'stuff');

        $this->assertNull($caching->put('name', 'stuff'));
    }

    public function testDelete()
    {
        $store = Mockery::mock(StoreInterface::class);
        $cache = Mockery::mock(Store::class);
        $caching = new CachingStore($store, new Repository($cache));

        $store->shouldReceive('delete')->once()->with('bar');
        $cache->shouldReceive('forget')->once()->with('store.bar');

        $this->assertNull($caching->delete('bar'));
    }
}
