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

use Illuminate\Contracts\Encryption\Encrypter;

/**
 * This is the encrypting store class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class EncryptingStore implements StoreInterface
{
    /**
     * The underlying store instance.
     *
     * @var \AltThree\Storage\Stores\StoreInterface
     */
    protected $store;

    /**
     * The encrypter instance.
     *
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * Create a new encrypting store instance.
     *
     * @param \AltThree\Storage\Stores\StoreInterface    $store
     * @param \Illuminate\Contracts\Encryption\Encrypter $encrypter
     *
     * @return void
     */
    public function __construct(StoreInterface $store, Encrypter $encrypter)
    {
        $this->store = $store;
        $this->encrypter = $encrypter;
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
    public function get(string $key)
    {
        if ($data = $this->store->get($key)) {
            return $this->encrypter->decrypt($data);
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
    public function put(string $key, string $data)
    {
        $this->store->put($key, $this->encrypter->encrypt($data));
    }

    /**
     * Delete an item from the storage if it exists.
     *
     * @param string $key
     *
     * @return void
     */
    public function delete(string $key)
    {
        $this->store->delete($key);
    }
}
