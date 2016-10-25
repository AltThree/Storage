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

use AltThree\Storage\Compressors\CompressorInterface;

/**
 * This is the compressing store class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class CompressingStore implements StoreInterface
{
    /**
     * The underlying store instance.
     *
     * @var \AltThree\Storage\Stores\StoreInterface
     */
    protected $store;

    /**
     * The compressor instance.
     *
     * @var \AltThree\Storage\Compressors\CompressorInterface
     */
    protected $compressor;

    /**
     * Create a new compressing store instance.
     *
     * @param \AltThree\Storage\Stores\StoreInterface           $store
     * @param \AltThree\Storage\Compressors\CompressorInterface $compressor
     *
     * @return void
     */
    public function __construct(StoreInterface $store, CompressorInterface $compressor)
    {
        $this->store = $store;
        $this->compressor = $compressor;
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
        if ($data = $this->store->get($key)) {
            return $this->compressor->uncompress($data);
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
        $this->store->put($key, $this->compressor->compress($data));
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
    }
}
