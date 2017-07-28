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
     * Get all keys from storage.
     *
     * @return string[]
     */
    public function all()
    {
        $keys = [];

        foreach ($this->flysystem->listContents() as $file) {
            $keys[] = $file['path'];
        }

        return $keys;
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
    public function put(string $key, string $data)
    {
        if (method_exists($this->flysystem, 'getConfig') && $this->flysystem->getConfig()->get('disable_asserts', false)) {
            $this->flysystem->write($key, $data);
        } else {
            $this->flysystem->put($key, $data);
        }
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
        try {
            $this->flysystem->delete($key);
        } catch (FileNotFoundException $e) {
            //
        }
    }
}
