<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use Iterator;
use IteratorAggregate;

final class Hotels implements IteratorAggregate
{
    use AsStreamableResult;

    public function getIterator(): Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['hotel_id'] => $item;
        }
    }
}
