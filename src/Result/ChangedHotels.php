<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use Bookingcom\Client\HotelChanges;
use Iterator;

final class ChangedHotels
{
    use AsStreamableResult;

    public function getClosedHotelIds(): Iterator
    {
        yield from $this->readJson('/result/closed_hotels');
    }

    public function getChanges(): Iterator
    {
        foreach ($this->readJson('/result/changed_hotels') as $item) {
            yield $item['hotel_id'] => HotelChanges::fromArray($item['changes']);
        }
    }
}
