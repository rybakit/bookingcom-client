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

final class FacilityType
{
    private string $name;
    private iterable $translations;

    private function __construct(string $name, iterable $translations)
    {
        $this->name = $name;
        $this->translations = $translations;
    }

    public static function fromArray(array $data) : self
    {
        if (empty($data['name'])) {
            throw new InvalidArgumentException('The "name" key is not found or empty.');
        }

        if (empty($data['translations'])) {
            throw new InvalidArgumentException('The "translations" key is not found or empty.');
        }

        return new self($data['name'], new Translations($data['translations']));
    }

    public function withTranslations(iterable $translations) : self
    {
        $self = clone $this;
        $self->translations = $translations;

        return $self;
    }

    public function getName() : string
    {
        return $this->name;
    }

    public function getTranslations() : iterable
    {
        return $this->translations;
    }
}
