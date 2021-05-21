<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use GuzzleHttp;
use Iterator;
use IteratorAggregate;

final class HotelFacilities implements IteratorAggregate
{
    private array $responses;

    private function __construct(array $responses)
    {
        $this->responses = $responses;
    }

    public static function fromResponses(array $responses): self
    {
        return new self($responses);
    }

    public function getIterator(): Iterator
    {
        foreach ($this->responses as $code => $response) {
            $body = GuzzleHttp\json_decode((string) $response->getBody(), true);

            if (isset($body['result'][0]['hotel_data']['hotel_facilities'])) {
                yield $code => $body['result'][0]['hotel_data']['hotel_facilities'];
            }
        }
    }
}
