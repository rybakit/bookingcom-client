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

use Bookingcom\Client\Exception\InvalidArgumentException;
use Bookingcom\Client\HotelFacilityType;

final class HotelFacilityTypes implements \IteratorAggregate
{
    use AsStreamableResult;

    /**
     * @return HotelFacilityType[]|\Iterator
     */
    public function getIterator() : \Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['hotel_facility_type_id'] => HotelFacilityType::fromArray($item);
        }
    }

    /**
     * Booking.com API has a bug (tested on version 2.3) when the resulting item
     * may contain "facility_type_id = 0". This is not a valid value for an identifier,
     * and the HotelFacilityTypes::getIterator() will throw an exception in this case,
     * hence this method which allows to skip invalid items.
     *
     * @return HotelFacilityType[]|\Iterator
     */
    public function filterInvalidItems() : \Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            try {
                yield $item['hotel_facility_type_id'] => HotelFacilityType::fromArray($item);
            } catch (InvalidArgumentException $e) {
                // try next
            }
        }
    }
}
