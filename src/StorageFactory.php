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

use AltThree\Storage\Compressors\CompressorInterface;
use AltThree\Storage\Stores\CachingStore;
use AltThree\Storage\Stores\CompressingStore;
use AltThree\Storage\Stores\EncryptingStore;
use AltThree\Storage\Stores\FallbackStore;
use AltThree\Storage\Stores\FlysystemStore;
use GrahamCampbell\Flysystem\FlysystemManager;
use Illuminate\Contracts\Cache\Factory;
use Illuminate\Contracts\Encryption\Encrypter;
use InvalidArgumentException;

/**
 * This is the storage factory class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class StorageFactory
{
    /**
     * The cache instance.
     *
     * @var \Illuminate\Contracts\Cache\Factory
     */
    protected $cache;

    /**
     * The encrypter instance.
     *
     * @var \Illuminate\Contracts\Encryption\Encrypter
     */
    protected $encrypter;

    /**
     * The flysystem instance.
     *
     * @var \GrahamCampbell\Flysystem\FlysystemManager
     */
    protected $flysystem;

    /**
     * The compressor instance.
     *
     * @var \AltThree\Storage\Compressors\CompressorInterface
     */
    protected $compressor;

    /**
     * Create a new storage factory instance.
     *
     * @param \Illuminate\Contracts\Cache\Factory               $cache
     * @param \Illuminate\Contracts\Encryption\Encrypter        $encrypter
     * @param \GrahamCampbell\Flysystem\FlysystemManager        $flysystem
     * @param \AltThree\Storage\Compressors\CompressorInterface $compressor
     *
     * @return void
     */
    public function __construct(Factory $cache, Encrypter $encrypter, FlysystemManager $flysystem, CompressorInterface $compressor)
    {
        $this->cache = $cache;
        $this->encrypter = $encrypter;
        $this->flysystem = $flysystem;
        $this->compressor = $compressor;
    }

    /**
     * Make a new store.
     *
     * @param array $config
     *
     * @throws \InvalidArgumentException
     *
     * @return \AltThree\Storage\Stores\StoreInterface
     */
    public function make(array $config)
    {
        $config = $this->getConfig($config);

        $store = new FlysystemStore($this->flysystem->connection($config['main']));

        if ($config['fallback']) {
            $store = new FallbackStore($store, new FlysystemStore($this->flysystem->connection($config['fallback'])));
        }

        if ($config['compression']) {
            $store = new CompressingStore($store, $this->compressor);
        }

        if ($config['encryption']) {
            if ($config['compression']) {
                throw new InvalidArgumentException('Compression cannot be enabled at the same time as encryption.');
            }

            $store = new EncryptingStore($store, $this->encrypter);
        }

        if ($cache = $config['cache']) {
            $store = new CachingStore($store, $this->cache->store(($cache === true) ? null : $cache));
        }

        return $store;
    }

    /**
     * Get the configuration data.
     *
     * @param string[] $config
     *
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    protected function getConfig(array $config)
    {
        if (!array_key_exists('main', $config) || !array_key_exists('fallback', $config)) {
            throw new InvalidArgumentException('The store requires flysystem configuration.');
        }

        if (!array_key_exists('cache', $config)) {
            throw new InvalidArgumentException('The store requires cache configuration.');
        }

        if (!array_key_exists('encryption', $config)) {
            throw new InvalidArgumentException('The store requires encryption configuration.');
        }

        if (!array_key_exists('compression', $config)) {
            throw new InvalidArgumentException('The store requires compression configuration.');
        }

        return array_merge(array_only($config, ['main', 'fallback', 'cache', 'encryption', 'compression']));
    }
}
