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

use Bookingcom\Client\Result\HotelDescriptions;

final class HotelDescriptionsTest extends TestCase
{
    public function testGetHotelDescriptions() : void
    {
        $langCodes = ['en', 'nl'];

        self::$client->getHotelDescriptions(10004, $langCodes)->then(static function (HotelDescriptions $descriptions) use ($langCodes) {
            $count = 0;
            foreach ($descriptions as $langCode => $description) {
                self::assertContains($langCode, $langCodes);
                self::assertNotEmpty($description);
                ++$count;
            }

            self::assertSame(\count($langCodes), $count);
        })->wait();
    }
}
