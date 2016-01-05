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
use AltThree\Storage\Compressors\ZlibCompressor;
use AltThree\Storage\Stores\StoreInterface;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\ServiceProvider;

/**
 * This is the storage service provider class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class StorageServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        $this->setupConfig();
    }

    /**
     * Setup the config.
     *
     * @return void
     */
    protected function setupConfig()
    {
        $source = realpath(__DIR__.'/../config/storage.php');

        if (class_exists('Illuminate\Foundation\Application', false)) {
            $this->publishes([$source => config_path('storage.php')]);
        }

        $this->mergeConfigFrom($source, 'storage');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerCompressor($this->app);
        $this->registerFactory($this->app);
        $this->registerManager($this->app);
        $this->registerBindings($this->app);
    }

    /**
     * Register the compressor class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerCompressor(Application $app)
    {
        $app->singleton('storage.compressor', function () {
            return new ZlibCompressor();
        });

        $app->alias('storage.compressor', CompressorInterface::class);
    }

    /**
     * Register the factory class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerFactory(Application $app)
    {
        $app->singleton('storage.factory', function (Application $app) {
            $cache = $app['cache'];
            $encrypter = $app['encrypter'];
            $flysystem = $app['flysystem'];
            $compressor = $app['storage.compressor'];

            return new StorageFactory($cache, $encrypter, $flysystem, $compressor);
        });

        $app->alias('storage.factory', StorageFactory::class);
    }

    /**
     * Register the manager class.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerManager(Application $app)
    {
        $app->singleton('storage', function (Application $app) {
            $config = $app['config'];
            $factory = $app['storage.factory'];

            return new StorageManager($config, $factory);
        });

        $app->alias('storage', StorageManager::class);
    }

    /**
     * Register the bindings.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     *
     * @return void
     */
    protected function registerBindings(Application $app)
    {
        $app->bind('storage.connection', function (Application $app) {
            $manager = $app['storage'];

            return $manager->connection();
        });

        $app->alias('storage.connection', StoreInterface::class);
    }

    /**
     * Get the services provided by the provider.
     *
     * @return string[]
     */
    public function provides()
    {
        return [
            'storage.compressor',
            'storage.factory',
            'storage',
            'storage.connection',
        ];
    }
}
