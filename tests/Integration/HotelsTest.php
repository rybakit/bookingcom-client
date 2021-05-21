<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Integration;

use Bookingcom\Client\Result\Hotels;

final class HotelsTest extends TestCase
{
    public function testGetHotelsByIds(): void
    {
        self::$client->getHotelsByIds([10003, 10004])->then(function (Hotels $hotels) {
            foreach ($hotels as $hotelId => $hotel) {
                self::assertIsInt($hotelId);
                self::assertTrue(isset($hotel['hotel_data']['hotel_facilities']), 'hotel_data/hotel_facilities is missing.');
                self::assertTrue(isset($hotel['hotel_data']['hotel_photos']), 'hotel_data/hotel_photos is missing.');
                self::assertTrue(isset($hotel['room_data'][0]['room_info']['min_price']), 'room_data/room_info/0/min_price is missing.');

                return;
            }

            $this->addWarning('No hotels were found.');
        })->wait();
    }
}
