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

use Bookingcom\Client\Result\HotelTypes;

final class HotelTypesTest extends TestCase
{
    public function testGetHotelTypes() : void
    {
        $langCodes = ['en', 'nl'];

        self::$client->getHotelTypes($langCodes)->then(static function (HotelTypes $types) use ($langCodes) {
            $count = 0;
            foreach ($types as $typeId => $type) {
                self::assertIsInt($typeId);
                self::assertNotEmpty($type->getName());
                ++$count;

                // "Uncertain" type doesn't have translations
                if (!$translations = $type->getTranslations()) {
                    continue;
                }

                self::assertAtLeastOneTranslation($langCodes, $translations);
            }

            self::assertGreaterThan(0, $count);
        })->wait();
    }
}
