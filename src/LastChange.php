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

namespace Bookingcom\Client;

use Bookingcom\Client\Exception\InvalidArgumentException;

final class LastChange
{
    private const DEFAULT_TIMEZONE = 'Europe/Amsterdam';

    private \DateTimeImmutable $datetime;

    private function __construct(\DateTimeImmutable $datetime)
    {
        if ($datetime <= new \DateTimeImmutable('-2 days', $datetime->getTimezone())) {
            throw new InvalidArgumentException('The date must be less than two days.');
        }

        $this->datetime = $datetime;
    }

    public static function fromString(string $time, string $timezone = self::DEFAULT_TIMEZONE) : self
    {
        try {
            $datetime = new \DateTimeImmutable($time, new \DateTimeZone($timezone));
        } catch (\Throwable $e) {
            throw new InvalidArgumentException(\sprintf('Failed to parse time "%s".', $time), 0, $e);
        }

        return new self($datetime);
    }

    public static function fromMax(string $timezone = self::DEFAULT_TIMEZONE) : self
    {
        // 30 seconds reserved for establishing the connection
        return self::fromString('-2 days 30 seconds', $timezone);
    }

    public function toString() : string
    {
        return $this->datetime->format('Y-m-d H:i:s');
    }
}
