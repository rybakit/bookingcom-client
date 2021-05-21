<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Integration;

use Bookingcom\Client\Result\HotelTypes;

final class HotelTypesTest extends TestCase
{
    public function testGetHotelTypes(): void
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
