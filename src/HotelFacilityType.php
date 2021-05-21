<?php

declare(strict_types=1);

namespace Bookingcom\Client;

use Bookingcom\Client\Exception\InvalidArgumentException;

final class HotelFacilityType
{
    private int $facilityTypeId;
    private string $name;
    private string $type;
    private iterable $translations;

    private function __construct(
        int $facilityTypeId,
        string $name,
        string $type,
        iterable $translations
    ) {
        $this->facilityTypeId = $facilityTypeId;
        $this->name = $name;
        $this->type = $type;
        $this->translations = $translations;
    }

    public static function fromArray(array $data): self
    {
        $data = array_filter($data);

        if ($diff = array_diff(['facility_type_id', 'name', 'type', 'translations'], array_keys($data))) {
            throw new InvalidArgumentException(sprintf('Missing or empty field(s): "%s".', implode('", "', $diff)));
        }

        return new self(
            $data['facility_type_id'],
            $data['name'],
            $data['type'],
            new Translations($data['translations'])
        );
    }

    public function withTranslations(iterable $translations): self
    {
        $self = clone $this;
        $self->translations = $translations;

        return $self;
    }

    public function getFacilityTypeId(): int
    {
        return $this->facilityTypeId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTranslations(): iterable
    {
        return $this->translations;
    }
}
