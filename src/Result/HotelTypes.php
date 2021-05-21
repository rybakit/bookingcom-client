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

use Bookingcom\Client\HotelType;

final class HotelTypes implements \IteratorAggregate
{
    use AsStreamableResult;

    /**
     * @return HotelType[]|\Iterator
     */
    public function getIterator() : \Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['hotel_type_id'] => HotelType::fromArray($item);
        }
    }
}
