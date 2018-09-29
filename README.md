# Alt Three Storage

A cached secure storage provider for Laravel 5.


## Installation

This version requires [PHP](https://php.net) 7.1 or 7.2, and supports Laravel 5.5 - 5.7 only.

To get the latest version, simply require the project using [Composer](https://getcomposer.org):

```bash
$ composer require alt-three/storage
```

Once installed, if you are not using automatic package discovery, then you need to register the `AltThree\Storage\StorageServiceProvider` service provider in your `config/app.php`.


## Configuration

Alt Three Storage requires configuration.

To get started, you'll need to publish all vendor assets:

```bash
$ php artisan vendor:publish
```

This will create a `config/login.php` file in your app that you can modify to set your configuration. Also, make sure you check for changes to the original config file in this package between releases.


## Security

If you discover a security vulnerability within this package, please e-mail us at support@alt-three.com. All security vulnerabilities will be promptly addressed.


## License

Alt Three Storage is licensed under [The MIT License (MIT)](LICENSE).
