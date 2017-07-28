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

namespace AltThree\Storage\Stores;

/**
 * This is the fallback store class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class FallbackStore implements StoreInterface
{
    /**
     * The main store instance.
     *
     * @var \AltThree\Storage\Stores\StoreInterface
     */
    protected $main;

    /**
     * The fallback store instance.
     *
     * @var \AltThree\Storage\Stores\StoreInterface
     */
    protected $fallback;

    /**
     * Create a new fallback store instance.
     *
     * @param \AltThree\Storage\Stores\StoreInterface $main
     * @param \AltThree\Storage\Stores\StoreInterface $fallback
     *
     * @return void
     */
    public function __construct(StoreInterface $main, StoreInterface $fallback)
    {
        $this->main = $main;
        $this->fallback = $fallback;
    }

    /**
     * Get all keys from storage.
     *
     * @return string[]
     */
    public function all()
    {
        return array_values(array_unique(array_merge($this->main->all(), $this->fallback->all())));
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
        if ($data = $this->main->get($key)) {
            return $data;
        }

        return $this->fallback->get($key);
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
        $this->main->put($key, $data);
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
        $this->main->delete($key);
        $this->fallback->delete($key);
    }
}
