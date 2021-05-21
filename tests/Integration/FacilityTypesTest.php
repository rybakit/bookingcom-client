<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Integration;

use Bookingcom\Client\Result\FacilityTypes;

final class FacilityTypesTest extends TestCase
{
    public function testGetFacilityTypes(): void
    {
        $langCodes = ['en', 'nl'];

        self::$client->getFacilityTypes($langCodes)->then(static function (FacilityTypes $types) use ($langCodes) {
            $count = 0;
            foreach ($types as $typeId => $type) {
                self::assertIsInt($typeId);
                self::assertNotEmpty($type->getName());
                self::assertAtLeastOneTranslation($langCodes, $type->getTranslations());
                ++$count;
            }

            self::assertGreaterThan(0, $count);
        })->wait();
    }
}
