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

namespace Bookingcom\Client\Result;

final class Hotels implements \IteratorAggregate
{
    use AsStreamableResult;

    public function getIterator() : \Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['hotel_id'] => $item;
        }
    }
}
