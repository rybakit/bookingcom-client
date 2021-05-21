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

namespace Bookingcom\Client\Tests\Integration;

use Bookingcom\Client\HotelChanges;
use Bookingcom\Client\LastChange;
use Bookingcom\Client\Result\ChangedHotels;

final class ChangedHotelsTest extends TestCase
{
    public function testGetClosedHotelIds() : void
    {
        $lastChange = LastChange::fromString('-1 day');

        self::$client->getChangedHotels($lastChange)->then(function (ChangedHotels $changedHotels) {
            foreach ($changedHotels->getClosedHotelIds() as $hotelId) {
                self::assertIsInt($hotelId);

                return;
            }

            $this->addWarning('No closed hotels were found.');
        })->wait();
    }

    public function testGetChanges() : void
    {
        $lastChange = LastChange::fromString('-1 day');

        self::$client->getChangedHotels($lastChange)->then(function (ChangedHotels $changedHotels) {
            foreach ($changedHotels->getChanges() as $hotelId => $changes) {
                self::assertIsInt($hotelId);
                self::assertInstanceOf(HotelChanges::class, $changes);

                return;
            }

            $this->addWarning('No changed hotels were found.');
        })->wait();
    }
}
