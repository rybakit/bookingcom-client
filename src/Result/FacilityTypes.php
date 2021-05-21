<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use Bookingcom\Client\FacilityType;
use Iterator;
use IteratorAggregate;

final class FacilityTypes implements IteratorAggregate
{
    use AsStreamableResult;

    /**
     * @return FacilityType[]|Iterator
     */
    public function getIterator(): Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['facility_type_id'] => FacilityType::fromArray($item);
        }
    }
}
