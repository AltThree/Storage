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

namespace AltThree\Storage\Compressors;

use RuntimeException;

/**
 * This is the zlib compressor class.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
class ZlibCompressor implements CompressorInterface
{
    /**
     * The compression level.
     *
     * @var int
     */
    protected $level;

    /**
     * Create a new zlib compressor instance.
     *
     * @param int $level
     *
     * @return void
     */
    public function __construct(int $level = 6)
    {
        $this->level = $level;
    }

    /**
     * Compress a string.
     *
     * @param string $data
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compress(string $data)
    {
        if (is_string($compressed = @gzcompress($data, $this->level))) {
            return $compressed;
        }

        throw new RuntimeException('Failed to compress the data.');
    }

    /**
     * Uncompress a string.
     *
     * @param string $compressed
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function uncompress(string $compressed)
    {
        if (is_string($data = @gzuncompress($compressed))) {
            return $data;
        }

        throw new RuntimeException('Failed to uncompress the data.');
    }
}
