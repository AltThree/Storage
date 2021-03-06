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

namespace AltThree\Tests\Storage\Compressors;

use AltThree\Storage\Compressors\ZlibCompressor;
use GrahamCampbell\TestBench\AbstractTestCase;

/**
 * This is the zlib compressor test class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ZlibCompressorTest extends AbstractTestCase
{
    public function testDefaultLevel()
    {
        $compressor = new ZlibCompressor();

        $original = 'foo!!!!!!!!!!!!!!!!!!!';
        $compressed = $compressor->compress($original);
        $data = $compressor->uncompress($compressed);

        $this->assertSame(13, strlen($compressed));
        $this->assertSame($original, $data);
    }

    public function testLowLevel()
    {
        $compressor = new ZlibCompressor(0);

        $original = 'foo!!!!!!!!!!!!!!!!!!!';
        $compressed = $compressor->compress($original);
        $data = $compressor->uncompress($compressed);

        $this->assertSame(33, strlen($compressed));
        $this->assertSame($original, $data);
    }

    /**
     * @expectedException TypeError
     */
    public function testBadCompress()
    {
        $compressor = new ZlibCompressor();

        $compressor->compress([]);
    }

    /**
     * @expectedException TypeError
     */
    public function testBadUncompress()
    {
        $compressor = new ZlibCompressor();

        $compressor->uncompress(123);
    }
}
