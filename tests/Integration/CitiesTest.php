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

use Bookingcom\Client\Result\Cities;

final class CitiesTest extends TestCase
{
    public function testGetCities() : void
    {
        $langCodes = ['en', 'nl'];

        self::$client->getCities($langCodes, $limit = 2)->then(static function (Cities $cities) use ($langCodes, $limit) {
            $count = 0;
            foreach ($cities as $cityId => $city) {
                self::assertIsInt($cityId);
                self::assertNotEmpty($city->getName());
                self::assertNotEmpty($city->getCountryCode());
                self::assertNotEmpty($city->getLongitude());
                self::assertNotEmpty($city->getLatitude());
                self::assertIsIterable($city->getTranslations());
                self::assertAtLeastOneTranslation($langCodes, $city->getTranslations());
                ++$count;
            }

            self::assertSame($limit, $count);
        })->wait();
    }
}
