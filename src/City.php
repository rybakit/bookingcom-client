<?php

declare(strict_types=1);

namespace Bookingcom\Client;

use Bookingcom\Client\Exception\InvalidArgumentException;

final class City
{
    private string $name;
    private string $countryCode;
    private float $longitude;
    private float $latitude;
    private iterable $translations;

    public function __construct(
        string $name,
        string $countryCode,
        float $longitude,
        float $latitude,
        iterable $translations
    ) {
        $this->name = $name;
        $this->countryCode = $countryCode;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
        $this->translations = $translations;
    }

    public static function fromArray(array $data): self
    {
        $data = array_filter($data);

        if ($diff = array_diff(['name', 'country', 'location', 'translations'], array_keys($data))) {
            throw new InvalidArgumentException(sprintf('Missing or empty field(s): "%s".', implode('", "', $diff)));
        }

        return new self(
            $data['name'],
            $data['country'],
            (float) $data['location']['longitude'],
            (float) $data['location']['latitude'],
            new Translations($data['translations'])
        );
    }

    public function withTranslations(iterable $translations): self
    {
        $self = clone $this;
        $self->translations = $translations;

        return $self;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCountryCode(): string
    {
        return $this->countryCode;
    }

    public function getLongitude(): float
    {
        return $this->longitude;
    }

    public function getLatitude(): float
    {
        return $this->latitude;
    }

    public function getTranslations(): iterable
    {
        return $this->translations;
    }
}
