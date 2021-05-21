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

use Bookingcom\Client\Exception\InvalidArgumentException;
use Bookingcom\Client\Result\HotelFacilityTypes;

final class HotelFacilityTypesTest extends TestCase
{
    public function testGetHotelFacilityTypes() : void
    {
        $langCodes = ['en', 'nl'];

        self::$client->getHotelFacilityTypes($langCodes)->then(static function (HotelFacilityTypes $types) use ($langCodes) {
            $count = 0;
            foreach ($types as $typeId => $type) {
                self::assertIsInt($typeId);
                self::assertIsInt($type->getFacilityTypeId());
                self::assertNotEmpty($type->getType());
                self::assertNotEmpty($type->getName());
                self::assertIsIterable($type->getTranslations());
                self::assertAtLeastOneTranslation($langCodes, $type->getTranslations());
                ++$count;
            }

            self::assertGreaterThan(0, $count);
        })->otherwise(static function (\Throwable $e) {
            if (!$e instanceof InvalidArgumentException) {
                throw $e;
            }
            if (!preg_match('/Missing or empty .+\bfacility_type_id\b/', $e->getMessage())) {
                throw $e;
            }

            // Booking.com API has a bug (tested on version 2.3) when the resulting item
            // may contain facility_type_id = 0.
            self::markTestSkipped('facility_type_id = 0 case detected');
        })->wait();
    }
}
