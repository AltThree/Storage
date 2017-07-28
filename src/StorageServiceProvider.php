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

namespace AltThree\Storage;

use AltThree\Storage\Compressors\CompressorInterface;
use AltThree\Storage\Compressors\ZlibCompressor;
use AltThree\Storage\Stores\StoreInterface;
use Illuminate\Contracts\Container\Container;
use Illuminate\Foundation\Application as LaravelApplication;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application as LumenApplication;

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

        if ($this->app instanceof LaravelApplication && $this->app->runningInConsole()) {
            $this->publishes([$source => config_path('storage.php')]);
        } elseif ($this->app instanceof LumenApplication) {
            $this->app->configure('storage');
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
        $this->registerCompressor();
        $this->registerFactory();
        $this->registerManager();
        $this->registerBindings();
    }

    /**
     * Register the compressor class.
     *
     * @return void
     */
    protected function registerCompressor()
    {
        $this->app->singleton('storage.compressor', function () {
            return new ZlibCompressor();
        });

        $this->app->alias('storage.compressor', CompressorInterface::class);
    }

    /**
     * Register the factory class.
     *
     * @return void
     */
    protected function registerFactory()
    {
        $this->app->singleton('storage.factory', function (Container $app) {
            $cache = $app['cache'];
            $encrypter = $app['encrypter'];
            $flysystem = $app['flysystem'];
            $compressor = $app['storage.compressor'];

            return new StorageFactory($cache, $encrypter, $flysystem, $compressor);
        });

        $this->app->alias('storage.factory', StorageFactory::class);
    }

    /**
     * Register the manager class.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('storage', function (Container $app) {
            $config = $app['config'];
            $factory = $app['storage.factory'];

            return new StorageManager($config, $factory);
        });

        $this->app->alias('storage', StorageManager::class);
    }

    /**
     * Register the bindings.
     *
     * @return void
     */
    protected function registerBindings()
    {
        $this->app->bind('storage.connection', function (Container $app) {
            $manager = $app['storage'];

            return $manager->connection();
        });

        $this->app->alias('storage.connection', StoreInterface::class);
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
            'storage.connection',
            'storage.factory',
            'storage',
        ];
    }
}
