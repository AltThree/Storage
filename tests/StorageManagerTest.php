<?php

/*
 * This file is part of Alt Three Storage.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AltThree\Tests\Storage;

use AltThree\Storage\StorageFactory;
use AltThree\Storage\StorageManager;
use AltThree\Storage\Stores\StoreInterface;
use GrahamCampbell\TestBench\AbstractTestCase;
use Illuminate\Contracts\Config\Repository;
use Mockery;

/**
 * This is the storage manager test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class StorageManagerTest extends AbstractTestCase
{
    public function testCreateConnection()
    {
        $config = ['token' => 'your-token'];

        $manager = $this->getManager($config);

        $manager->getConfig()->shouldReceive('get')->once()
            ->with('storage.default')->andReturn('main');

        $this->assertSame([], $manager->getConnections());

        $return = $manager->connection();

        $this->assertInstanceOf(StoreInterface::class, $return);

        $this->assertArrayHasKey('main', $manager->getConnections());
    }

    protected function getManager(array $config)
    {
        $repo = Mockery::mock(Repository::class);
        $factory = Mockery::mock(StorageFactory::class);

        $manager = new StorageManager($repo, $factory);

        $manager->getConfig()->shouldReceive('get')->once()
            ->with('storage.connections')->andReturn(['main' => $config]);

        $config['name'] = 'main';

        $manager->getFactory()->shouldReceive('make')->once()
            ->with($config)->andReturn(Mockery::mock(StoreInterface::class));

        return $manager;
    }
}
