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

/**
 * This is the compressor interface.
 *
 * @author Graham Campbell <graham@alt-three.com>
 */
interface CompressorInterface
{
    /**
     * Compress a string.
     *
     * @param string $data
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function compress(string $data);

    /**
     * Uncompress a string.
     *
     * @param string $compressed
     *
     * @throws \RuntimeException
     *
     * @return string
     */
    public function uncompress(string $compressed);
}
