<?php

namespace App\Calendar;

final readonly class SalesWindow
{
    public function __construct(
        public bool $open,
        public ?string $opens = null,
        public ?string $closes = null,
        public ?string $reason = null,
    ) {}

    public function range(): ?string
    {
        if (! $this->open) {
            return null;
        }

        return $this->hour($this->opens).'–'.$this->hour($this->closes);
    }

    private function hour(string $time): string
    {
        return str_ends_with($time, ':00') ? substr($time, 0, 2) : $time;
    }
}
