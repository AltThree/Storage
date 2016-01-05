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

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;

/**
 * This is the flysystem store class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class FlysystemStore implements StoreInterface
{
    /**
     * The flysystem instance.
     *
     * @var \League\Flysystem\FilesystemInterface
     */
    protected $flysystem;

    /**
     * Create a new storage factory instance.
     *
     * @param \League\Flysystem\FilesystemInterface $flysystem
     *
     * @return void
     */
    public function __construct(FilesystemInterface $flysystem)
    {
        $this->flysystem = $flysystem;
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
        try {
            if ($data = $this->flysystem->read($key)) {
                return $data;
            }
        } catch (FileNotFoundException $e) {
            //
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
        $this->flysystem->put($key, $data);
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
        try {
            $this->flysystem->delete($key);
        } catch (FileNotFoundException $e) {
            //
        }
    }
}
