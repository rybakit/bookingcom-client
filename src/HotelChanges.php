<?php

declare(strict_types=1);

namespace Bookingcom\Client;

final class HotelChanges
{
    private bool $hasDescription;
    private bool $hasFacilities;

    private function __construct(bool $hasDescription, bool $hasFacilities)
    {
        $this->hasDescription = $hasDescription;
        $this->hasFacilities = $hasFacilities;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            isset($data['hotel_description']),
            isset($data['hotel_facilities'])
        );
    }

    public static function fromAllChanged(): self
    {
        return new self(true, true);
    }

    public function hasDescription(): bool
    {
        return $this->hasDescription;
    }

    public function hasFacilities(): bool
    {
        return $this->hasFacilities;
    }
}
