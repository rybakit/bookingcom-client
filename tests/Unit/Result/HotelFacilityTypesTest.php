<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Unit\Result;

use GuzzleHttp\Psr7\Response;
use Bookingcom\Client\Result\HotelFacilityTypes;
use PHPUnit\Framework\TestCase;

final class HotelFacilityTypesTest extends TestCase
{
    /**
     * @dataProvider provideResultSetWithInvalidItemsData
     */
    public function testFilterInvalidItemsFiltersOutInvalidItems(array $expectedResultKeys, string $jsonBody): void
    {
        $response = new Response(200, [], $jsonBody);
        $types = HotelFacilityTypes::fromResponse($response);

        $all = iterator_to_array($types->filterInvalidItems());

        self::assertSame($expectedResultKeys, array_keys($all));
    }

    public function provideResultSetWithInvalidItemsData(): iterable
    {
        yield [[2, 3], '{"result":[
            {"name": "Bar", "type": "boolean", "facility_type_id": 0, "hotel_facility_type_id": 1, "translations": [{"language": "en-gb", "name": ""}]},
            {"name": "Foo", "type": "boolean", "facility_type_id": 2, "hotel_facility_type_id": 2, "translations": [{"language": "en-gb", "name": ""}]},
            {"name": "Baz", "type": "boolean", "facility_type_id": 3, "hotel_facility_type_id": 3, "translations": [{"language": "en-gb", "name": ""}]}
        ]}'];

        yield [[1, 3], '{"result":[
            {"name": "Bar", "type": "boolean", "facility_type_id": 1, "hotel_facility_type_id": 1, "translations": [{"language": "en-gb", "name": ""}]},
            {"name": "Foo", "type": "boolean", "facility_type_id": 0, "hotel_facility_type_id": 2, "translations": [{"language": "en-gb", "name": ""}]},
            {"name": "Baz", "type": "boolean", "facility_type_id": 3, "hotel_facility_type_id": 3, "translations": [{"language": "en-gb", "name": ""}]}
        ]}'];

        yield [[1, 2], '{"result":[
            {"name": "Bar", "type": "boolean", "facility_type_id": 1, "hotel_facility_type_id": 1, "translations": [{"language": "en-gb", "name": ""}]},
            {"name": "Foo", "type": "boolean", "facility_type_id": 2, "hotel_facility_type_id": 2, "translations": [{"language": "en-gb", "name": ""}]},
            {"name": "Baz", "type": "boolean", "facility_type_id": 0, "hotel_facility_type_id": 3, "translations": [{"language": "en-gb", "name": ""}]}
        ]}'];
    }
}
