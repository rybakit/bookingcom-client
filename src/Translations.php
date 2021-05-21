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

final class Translations implements \IteratorAggregate
{
    private array $translations;

    public function __construct(array $translations = [])
    {
        $this->translations = $translations;
    }

    public function getIterator() : \Iterator
    {
        foreach ($this->translations as $translation) {
            yield $translation['language'] => $translation['name'];
        }
    }
}
