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

use Bookingcom\Client\City;

final class Cities implements \IteratorAggregate
{
    use AsStreamableResult;

    /**
     * @return City[]|\Iterator
     */
    public function getIterator() : \Iterator
    {
        foreach ($this->readJson('/result') as $item) {
            yield $item['city_id'] => City::fromArray($item);
        }
    }
}
