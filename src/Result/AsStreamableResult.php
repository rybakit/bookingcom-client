<?php

declare(strict_types=1);

namespace Bookingcom\Client\Result;

use Bookingcom\Client\JsonReader;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

trait AsStreamableResult
{
    private JsonReader $jsonReader;

    public function __construct(StreamInterface $stream)
    {
        $this->jsonReader = new JsonReader($stream);
    }

    public function readJson(string $jsonPointer = ''): iterable
    {
        return $this->jsonReader->read($jsonPointer);
    }

    public static function fromResponse(ResponseInterface $response): self
    {
        return new self($response->getBody());
    }
}
