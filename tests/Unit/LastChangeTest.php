<?php

declare(strict_types=1);

namespace Bookingcom\Client\Tests\Unit;

use Bookingcom\Client\Exception\InvalidArgumentException;
use Bookingcom\Client\LastChange;
use PHPUnit\Framework\TestCase;

final class LastChangeTest extends TestCase
{
    public function testFromString(): void
    {
        $date = new \DateTimeImmutable('-1 hour');
        $dateString = $date->format('Y-m-d H:i:s');

        $lastChange = LastChange::fromString($dateString, $date->getTimezone()->getName());

        self::assertSame($dateString, $lastChange->toString());
    }

    public function testFromMax(): void
    {
        $date = new \DateTimeImmutable('-2 days 30 seconds');
        $lastChange = LastChange::fromMax($date->getTimezone()->getName());

        self::assertSame($date->format('Y-m-d H:i:s'), $lastChange->toString());
    }

    public function testFromExpiredDate(): void
    {
        LastChange::fromString('-2 days 1 sec');

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('The date must be less than two days.');
        LastChange::fromString('-2 days');
    }

    public function testFromInvalidDate(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Failed to parse time "foobar".');
        LastChange::fromString('foobar');
    }
}
