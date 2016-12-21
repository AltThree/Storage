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
     * The default cache ttl.
     *
     * @var int
     */
    const DEFAULT_TTL = 120;

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
     * The cache ttl.
     *
     * @var int
     */
    protected $ttl;

    /**
     * Create a new caching store instance.
     *
     * @param \AltThree\Storage\Stores\StoreInterface $store
     * @param \Illuminate\Contracts\Cache\Repository  $cache
     * @param int|null                                $ttl
     *
     * @return void
     */
    public function __construct(StoreInterface $store, Repository $cache, int $ttl = null)
    {
        $this->store = $store;
        $this->cache = $cache;
        $this->ttl = $ttl ?: self::DEFAULT_TTL;
    }

    /**
     * Get all keys from storage.
     *
     * @return string[]
     */
    public function all()
    {
        return $this->store->all();
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
        $data = $this->cache->remember("store.{$key}", $this->ttl, function () use ($key) {
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
        $this->cache->put("store.{$key}", $data, $this->ttl);
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
