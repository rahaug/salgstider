<?php

namespace App\Pages;

use Carbon\CarbonImmutable;

class DateSlug
{
    private const MONTHS = [
        1 => 'januar', 2 => 'februar', 3 => 'mars', 4 => 'april',
        5 => 'mai', 6 => 'juni', 7 => 'juli', 8 => 'august',
        9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember',
    ];

    public function __construct(
        public int $day,
        public int $month,
    ) {}

    public static function parse(string $slug): ?self
    {
        if (! preg_match('/^(\d{1,2})-([a-zæøå]+)$/u', $slug, $matches)) {
            return null;
        }

        $month = array_search($matches[2], self::MONTHS, true);

        if ($month === false) {
            return null;
        }

        $day = (int) $matches[1];

        if (! checkdate($month, $day, 2024)) {
            return null;
        }

        return new self($day, $month);
    }

    public function toString(): string
    {
        return $this->day.'-'.self::MONTHS[$this->month];
    }

    public function label(): string
    {
        return $this->day.'. '.self::MONTHS[$this->month];
    }

    public function nextOccurrence(CarbonImmutable $from): CarbonImmutable
    {
        $year = $from->year;

        while (true) {
            if (checkdate($this->month, $this->day, $year)) {
                $candidate = CarbonImmutable::create($year, $this->month, $this->day, 0, 0, 0, 'Europe/Oslo');

                if ($candidate >= $from->startOfDay()) {
                    return $candidate;
                }
            }

            $year++;
        }
    }

    /** @return list<self> */
    public static function all(): array
    {
        $slugs = [];

        foreach (array_keys(self::MONTHS) as $month) {
            $days = (int) CarbonImmutable::create(2024, $month, 1)->daysInMonth;

            for ($day = 1; $day <= $days; $day++) {
                $slugs[] = new self($day, $month);
            }
        }

        return $slugs;
    }
}
