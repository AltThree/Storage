<?php

/*
 * This file is part of Alt Three Storage.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Storage;

use GrahamCampbell\Manager\AbstractManager;
use Illuminate\Contracts\Config\Repository;

/**
 * This is the storage manager class.
 *
 * @method string|null get(string $key)
 * @method void put(string $key, string $data)
 * @method void delete(string $key)
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class StorageManager extends AbstractManager
{
    /**
     * The factory instance.
     *
     * @var \AltThree\Storage\StorageFactory
     */
    protected $factory;

    /**
     * Create a new storage manager instance.
     *
     * @param \Illuminate\Contracts\Config\Repository $config
     * @param \AltThree\Storage\StorageFactory         $factory
     *
     * @return void
     */
    public function __construct(Repository $config, StorageFactory $factory)
    {
        parent::__construct($config);
        $this->factory = $factory;
    }

    /**
     * Create the connection instance.
     *
     * @param array $config
     *
     * @return \AltThree\Storage\Stores\StoreInterface
     */
    protected function createConnection(array $config)
    {
        return $this->factory->make($config);
    }

    /**
     * Get the configuration name.
     *
     * @return string
     */
    protected function getConfigName()
    {
        return 'storage';
    }

    /**
     * Get the factory instance.
     *
     * @return \AltThree\Storage\StorageFactory
     */
    public function getFactory()
    {
        return $this->factory;
    }
}
