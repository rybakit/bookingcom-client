<?php

/**
 * This file is part of the bookingcom/client package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Integration;

use Bookingcom\Client\Exception\IOException;
use Bookingcom\Client\Result\AsStreamableResult;
use GuzzleHttp\Psr7\Stream;
use pcrov\JsonReader\InputStream\Psr7Stream;
use PHPUnit\Framework\TestCase;
use function GuzzleHttp\Promise\coroutine;
use function GuzzleHttp\Promise\settle;

final class JsonStreamTest extends TestCase
{
    public function testParallelReadThrowsIOException() : void
    {
        $total = (int) (Psr7Stream::CHUNK_SIZE * 1.5);

        $json = '{"nums": [';
        for ($i = 0; $i < $total; ++$i) {
            $json .= "$i,";
        }
        $json .= rtrim($json, ',').']';

        $stream = fopen('php://memory', 'rw');
        fwrite($stream, $json);
        rewind($stream);

        /** @var AsStreamableResult $result */
        $result = new class(new Stream($stream)) {
            use AsStreamableResult;
        };

        $nums1 = [-1 => -1];
        $nums2 = [-1 => -1];

        $promises[] = coroutine(function () use ($result, &$nums1) {
            foreach ($result->readJson('/nums') as $num) {
                if ($nums1[$num - 1] + 1 !== $num) {
                    self::fail('Reader skipped reading data.');
                }
                yield $nums1[$num] = $num;
            }
        });

        $promises[] = coroutine(function () use ($result, &$nums2) {
            $this->expectException(IOException::class);
            $this->expectExceptionMessage('Stream is already in use.');

            foreach ($result->readJson('/nums') as $num) {
                if ($nums2[$num - 1] + 1 !== $num) {
                    self::fail('Reader skipped reading data.');
                }
                yield $nums2[$num] = $num;
            }
        });

        settle($promises)->wait();
    }

    public function testSequentialReadResetsPointer() : void
    {
        $stream = fopen('php://memory', 'rw');
        fwrite($stream, '{"nums": [1, 2, 3]}');
        rewind($stream);

        /** @var AsStreamableResult $result */
        $result = new class(new Stream($stream)) {
            use AsStreamableResult;
        };

        $nums = [];
        foreach ($result->readJson('/nums') as $num) {
            $nums[] = $num;
        }
        self::assertSame([1, 2, 3], $nums);

        $nums = [];
        foreach ($result->readJson('/nums') as $num) {
            $nums[] = $num;
        }
        self::assertSame([1, 2, 3], $nums);
    }
}
