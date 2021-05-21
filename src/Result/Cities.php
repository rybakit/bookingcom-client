<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use Bookingcom\Client\City;
use Iterator;
use IteratorAggregate;

final class Cities implements IteratorAggregate
{
    use AsStreamableResult;

    /**
     * @return City[]|Iterator
     */
    public function getIterator(): Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['city_id'] => City::fromArray($item);
        }
    }
}
