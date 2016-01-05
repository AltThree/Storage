<?php

/*
 * This file is part of Alt Three Storage.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Storage Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application.
    |
    | The main flysystem setting tells us which of your configured flysystem
    | connections to use, and a fallback connection can be provided if needed.
    |
    | The cache setting tells which cache driver to use. You can set this to
    | false to disabled caching.
    |
    | The encryption setting tells us if you want us to encrypt your data in
    | the storage.
    |
    | The compression setting tells us if you want us to compress your data in
    | the storage.
    |
    */

    'connections' => [

        'default' => [
            'main'        => 'local',
            'fallback'    => null,
            'cache'       => 'file',
            'encryption'  => false,
            'compression' => false,
        ],

    ],

];
