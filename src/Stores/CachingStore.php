<?php

/*
 * This file is part of Alt Three Storage.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Storage\Stores;

use Illuminate\Contracts\Cache\Repository;

/**
 * This is the caching store class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CachingStore implements StoreInterface
{
    /**
     * The underlying store instance.
     *
     * @var \AltThree\Storage\Stores\StoreInterface
     */
    protected $store;

    /**
     * The cache instance.
     *
     * @var \Illuminate\Contracts\Cache\Repository
     */
    protected $cache;

    /**
     * Create a new caching store instance.
     *
     * @param \AltThree\Storage\Stores\StoreInterface $store
     * @param \Illuminate\Contracts\Cache\Repository  $cache
     *
     * @return void
     */
    public function __construct(StoreInterface $store, Repository $cache)
    {
        $this->store = $store;
        $this->cache = $cache;
    }

    /**
     * Get an item from the storage.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get($key)
    {
        $data = $this->cache->remember("store.{$key}", 30, function () use ($key) {
            return $this->store->get($key);
        });

        if ($data) {
            return $data;
        }
    }

    /**
     * Put an item into the storage.
     *
     * @param string $key
     * @param string $data
     *
     * @return void
     */
    public function put($key, $data)
    {
        $this->cache->put("store.{$key}", $data, 30);
        $this->store->put($key, $data);
    }

    /**
     * Delete an item from the storage if it exists.
     *
     * @param string $key
     *
     * @return void
     */
    public function delete($key)
    {
        $this->store->delete($key);
        $this->cache->forget("store.{$key}");
    }
}
