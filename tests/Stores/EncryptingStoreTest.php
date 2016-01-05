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

use AltThree\Storage\Stores\EncryptingStore;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Encryption\Encrypter;
use Mockery;

/**
 * This is the encrypting store test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class EncryptingStoreTest extends AbstractTestCase
{
    public function testGetData()
    {
        $store = Mockery::mock(StoreInterface::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $encrypting = new EncryptingStore($store, $encrypter);

        $store->shouldReceive('get')->once()->with('foo')->andReturn('raw');
        $encrypter->shouldReceive('decrypt')->once()->with('raw')->andReturn('data');

        $this->assertSame('data', $encrypting->get('foo'));
    }

    public function testGetEmpty()
    {
        $store = Mockery::mock(StoreInterface::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $encrypting = new EncryptingStore($store, $encrypter);

        $store->shouldReceive('get')->once()->with('baz');

        $this->assertNull($encrypting->get('baz'));
    }

    public function testPut()
    {
        $store = Mockery::mock(StoreInterface::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $encrypting = new EncryptingStore($store, $encrypter);

        $encrypter->shouldReceive('encrypt')->once()->with('data')->andReturn('raw');
        $store->shouldReceive('put')->once()->with('bar', 'raw');

        $this->assertNull($encrypting->put('bar', 'data'));
    }

    public function testDelete()
    {
        $store = Mockery::mock(StoreInterface::class);
        $encrypter = Mockery::mock(Encrypter::class);
        $encrypting = new EncryptingStore($store, $encrypter);

        $store->shouldReceive('delete')->once()->with('bar');

        $this->assertNull($encrypting->delete('bar'));
    }
}
