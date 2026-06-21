<?php

namespace App\Calendar;

use Carbon\CarbonImmutable;
use Yasumi\Holiday;
use Yasumi\Yasumi;

class DayClassifier
{
    private const PUBLIC_HOLIDAY_KEYS = [
        'newYearsDay', 'maundyThursday', 'goodFriday', 'easter', 'easterMonday',
        'ascensionDay', 'pentecost', 'pentecostMonday', 'christmasDay', 'secondChristmasDay',
    ];

    private const EXTRA_NO_SALE_KEYS = ['internationalWorkersDay', 'constitutionDay'];

    private const EVE_EXEMPT_KEYS = ['ascensionDay'];

    /** @var array<int, array<string, Holiday>> */
    private array $byDate = [];

    public function isPublicHoliday(CarbonImmutable $date): bool
    {
        return $date->isSunday() || $this->keyOn($date, self::PUBLIC_HOLIDAY_KEYS);
    }

    public function isNoSaleDay(CarbonImmutable $date): bool
    {
        return $date->isSunday()
            || $this->keyOn($date, [...self::PUBLIC_HOLIDAY_KEYS, ...self::EXTRA_NO_SALE_KEYS]);
    }

    public function isReducedEve(CarbonImmutable $date): bool
    {
        $tomorrow = $date->addDay();

        return $this->isPublicHoliday($tomorrow) && ! $this->keyOn($tomorrow, self::EVE_EXEMPT_KEYS);
    }

    public function isChristmasEve(CarbonImmutable $date): bool
    {
        return $date->month === 12 && $date->day === 24;
    }

    public function isNamedHoliday(CarbonImmutable $date): bool
    {
        return $this->holidayOn($date) !== null;
    }

    public function isStoreClosingEve(CarbonImmutable $date): bool
    {
        return $this->isChristmasEve($date) || $this->keyOn($date->addDay(), ['easter', 'pentecost']);
    }

    public function eveName(CarbonImmutable $date): ?string
    {
        return match (true) {
            $this->isChristmasEve($date) => 'Julaften',
            $this->keyOn($date->addDay(), ['easter']) => 'Påskeaften',
            $this->keyOn($date->addDay(), ['pentecost']) => 'Pinseaften',
            $this->keyOn($date->addDay(), ['newYearsDay']) => 'Nyttårsaften',
            default => null,
        };
    }

    public function observanceName(CarbonImmutable $date): ?string
    {
        return match (true) {
            $date->month === 3 && $date->day === 8 => 'kvinnedagen',
            $date->month === 5 && $date->day === 8 => 'frigjøringsdagen',
            $this->isEasterRelative($date, -49) => 'fastelavn',
            $this->isEasterRelative($date, -7) => 'palmesøndag',
            $this->isLastSundayOfOctober($date) => 'bots- og bededag',
            $this->isFirstSundayOfNovember($date) => 'allehelgensdag',
            default => null,
        };
    }

    public function namedDay(CarbonImmutable $date): ?string
    {
        if ($this->isNamedHoliday($date)) {
            return $this->name($date);
        }

        return $this->eveName($date) ?? $this->observanceName($date);
    }

    public function dateOf(int $year, string $key): ?CarbonImmutable
    {
        foreach ($this->byDate[$year] ??= $this->buildMap($year) as $ymd => $holiday) {
            if ($holiday->getKey() === $key) {
                return CarbonImmutable::createFromFormat('Y-m-d', $ymd, 'Europe/Oslo')->startOfDay();
            }
        }

        return null;
    }

    public function name(CarbonImmutable $date): ?string
    {
        if ($holiday = $this->holidayOn($date)) {
            return $holiday->getName();
        }

        if ($this->isChristmasEve($date)) {
            return 'Julaften';
        }

        return $date->isSunday() ? 'Søndag' : null;
    }

    private function isEasterRelative(CarbonImmutable $date, int $offset): bool
    {
        $easter = $this->dateOf($date->year, 'easter');

        return $easter !== null && $easter->addDays($offset)->isSameDay($date);
    }

    private function isLastSundayOfOctober(CarbonImmutable $date): bool
    {
        return $date->month === 10 && $date->isSunday() && $date->addDays(7)->month === 11;
    }

    private function isFirstSundayOfNovember(CarbonImmutable $date): bool
    {
        return $date->month === 11 && $date->isSunday() && $date->day <= 7;
    }

    /** @param list<string> $keys */
    private function keyOn(CarbonImmutable $date, array $keys): bool
    {
        $holiday = $this->holidayOn($date);

        return $holiday !== null && in_array($holiday->getKey(), $keys, true);
    }

    private function holidayOn(CarbonImmutable $date): ?Holiday
    {
        return ($this->byDate[$date->year] ??= $this->buildMap($date->year))[$date->format('Y-m-d')] ?? null;
    }

    /** @return array<string, Holiday> */
    private function buildMap(int $year): array
    {
        $map = [];

        foreach (Yasumi::create('Norway', $year, 'nb_NO') as $holiday) {
            $map[$holiday->format('Y-m-d')] = $holiday;
        }

        return $map;
    }
}
