<?php

declare(strict_types=1);

namespace Bookingcom\Client;

final class Translations implements \IteratorAggregate
{
    private array $translations;

    public function __construct(array $translations = [])
    {
        $this->translations = $translations;
    }

    public function getIterator(): \Iterator
    {
        foreach ($this->translations as $translation) {
            yield $translation['language'] => $translation['name'];
        }
    }
}
