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

use Bookingcom\Client\Client;
use PHPUnit\Framework\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected static Client $client;

    /**
     * @beforeClass
     */
    public static function setUpClient() : void
    {
        if (!getenv('BOOKINGCOM_API_USERNAME') || !getenv('BOOKINGCOM_API_PASSWORD')) {
            self::markTestSkipped('Booking.com API credentials are not set');
        }

        self::$client = new Client(
            getenv('BOOKINGCOM_API_USERNAME'),
            getenv('BOOKINGCOM_API_PASSWORD')
        );
    }

    public static function assertAtLeastOneTranslation(array $langCodes, iterable $translations) : void
    {
        foreach ($translations as $langCode => $translation) {
            // normalize "en-gb" to "en"
            $normLangCode = strtolower(explode('-', $langCode, 2)[0]);

            self::assertContains($normLangCode, $langCodes);
            self::assertNotEmpty($translation);
        }
    }
}
