<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Integration;

use Bookingcom\Client\Result\HotelFacilities;

final class HotelFacilitiesTest extends TestCase
{
    public function testGetHotelFacilities(): void
    {
        $langCodes = ['en', 'nl'];

        self::$client->getHotelFacilities(10004, $langCodes)->then(static function (HotelFacilities $allFacilities) use ($langCodes) {
            $count = 0;
            foreach ($allFacilities as $langCode => $facilities) {
                self::assertContains($langCode, $langCodes);
                self::assertNotEmpty($facilities);
                ++$count;
            }

            self::assertSame(\count($langCodes), $count);
        })->wait();
    }
}
