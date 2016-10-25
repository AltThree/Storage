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

/**
 * This is the store interface.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
interface StoreInterface
{
    /**
     * Get all keys from storage.
     *
     * @return string[]
     */
    public function all();

    /**
     * Get an item from the storage.
     *
     * @param string $key
     *
     * @return string|null
     */
    public function get($key);

    /**
     * Put an item into the storage.
     *
     * @param string $key
     * @param string $data
     *
     * @return void
     */
    public function put($key, $data);

    /**
     * Delete an item from the storage if it exists.
     *
     * @param string $key
     *
     * @return void
     */
    public function delete($key);
}
