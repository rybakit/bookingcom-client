<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use Bookingcom\Client\HotelType;

final class HotelTypes implements \IteratorAggregate
{
    use AsStreamableResult;

    /**
     * @return HotelType[]|\Iterator
     */
    public function getIterator(): \Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['hotel_type_id'] => HotelType::fromArray($item);
        }
    }
}
