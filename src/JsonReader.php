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

namespace Bookingcom\Client;

use Bookingcom\Client\Exception\IOException;
use pcrov\JsonReader\JsonReader as PcrovJsonReader;
use Psr\Http\Message\StreamInterface;

final class JsonReader
{
    private StreamInterface $stream;
    private bool $isLocked = false;

    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }

    public function read(string $jsonPointer = '') : iterable
    {
        if ($this->isLocked) {
            throw new IOException('Stream is already in use.');
        }

        $this->isLocked = true;

        $reader = new PcrovJsonReader();

        try {
            $this->stream->rewind();
            $reader->psr7Stream($this->stream);

            if ($jsonPointer && !self::seekTo($reader, $jsonPointer)) {
                return;
            }

            $depth = $reader->depth();
            if (!$reader->read() || \in_array($reader->type(), [PcrovJsonReader::END_ARRAY, PcrovJsonReader::END_OBJECT], true)) {
                return;
            }

            do {
                yield $reader->value();
            } while ($reader->next() && $reader->depth() > $depth);
        } finally {
            $reader->close();
            $this->isLocked = false;
        }
    }

    private static function seekTo(PcrovJsonReader $reader, string $jsonPointer) : bool
    {
        if (!$nodes = \explode('/', \trim($jsonPointer, '/'))) {
            return false;
        }

        $node = \array_shift($nodes);
        if (!$reader->read($node)) {
            return false;
        }

        if (0 === \strpos($jsonPointer, '/') && 1 !== $reader->depth()) {
            return false;
        }

        foreach ($nodes as $node) {
            if (!$reader->read($node)) {
                return false;
            }
        }

        return true;
    }
}
