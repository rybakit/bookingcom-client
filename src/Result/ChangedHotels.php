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

use Bookingcom\Client\HotelChanges;

final class ChangedHotels
{
    use AsStreamableResult;

    public function getClosedHotelIds() : \Iterator
    {
        yield from $this->readJson('/result/closed_hotels');
    }

    public function getChanges() : \Iterator
    {
        foreach ($this->readJson('/result/changed_hotels') as $item) {
            yield $item['hotel_id'] => HotelChanges::fromArray($item['changes']);
        }
    }
}
