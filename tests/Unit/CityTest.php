<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Unit;

use Bookingcom\Client\City;
use PHPUnit\Framework\TestCase;

final class CityTest extends TestCase
{
    public function testFromArray(): void
    {
        $city = City::fromArray([
            'name' => $name = 'Foobar',
            'country' => $countryCode = 'en',
            'location' => [
                'longitude' => $longitude = 1.2,
                'latitude' => $latitude = 3.4,
            ],
            'translations' => [[
                'language' => $transLangCode = 'en',
                'name' => $transName = 'FoobarEn',
            ]],
        ]);

        self::assertSame($name, $city->getName());
        self::assertSame($countryCode, $city->getCountryCode());
        self::assertSame($longitude, $city->getLongitude());
        self::assertSame($latitude, $city->getLatitude());

        foreach ($city->getTranslations() as $langCode => $translation) {
            self::assertSame($transLangCode, $langCode);
            self::assertSame($transName, $translation);
        }
    }
}
