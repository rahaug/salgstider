<?php

namespace App\View\Components;

use App\Calendar\DayClassifier;
use App\Calendar\OpeningHours;
use App\Enums\ProductType;
use App\Pages\DateSlug;
use Carbon\CarbonImmutable;
use Illuminate\View\Component;
use Illuminate\View\View;

class TodayCard extends Component
{
    private const AVVIK_WINDOW_DAYS = 30;

    /** @var array{label: string, beer: array<string, mixed>, wine: array<string, mixed>} */
    public array $today;

    /** @var ?array<string, mixed> */
    public ?array $card;

    /** @var ?array{slug: string, name: string, date: string} */
    public ?array $below;

    /**
     * @param  ?array{scope: string, date: ?CarbonImmutable}  $avvik  Month-scoped override; null = global (home).
     */
    public function __construct(
        private DayClassifier $classifier,
        private OpeningHours $hours,
        ?array $avvik = null,
    ) {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();

        $this->today = $this->buildToday($today);

        if ($avvik !== null) {
            [$this->card, $this->below] = $this->contextCard($today, $avvik['scope'], $avvik['date'] ?? null);
        } else {
            [$this->card, $this->below] = $this->globalCard($today);
        }
    }

    public function render(): View
    {
        return view('components.today-card');
    }

    /** @return array{label: string, beer: array<string, mixed>, wine: array<string, mixed>} */
    private function buildToday(CarbonImmutable $today): array
    {
        return [
            'label' => ucfirst($today->locale('nb')->isoFormat('dddd D. MMMM')),
            'beer' => $this->status($today, ProductType::Beer),
            'wine' => $this->status($today, ProductType::Wine),
        ];
    }

    /** @return array{open: bool, range: ?string, next: ?array{day: string, opens: string}} */
    private function status(CarbonImmutable $today, ProductType $type): array
    {
        $window = $this->hours->on($today, $type);

        return [
            'open' => $window->open,
            'range' => $window->range(),
            'next' => $window->open ? null : $this->nextOpen($today, $type),
        ];
    }

    /** @return ?array{day: string, opens: string} */
    private function nextOpen(CarbonImmutable $today, ProductType $type): ?array
    {
        $cursor = $today->addDay();

        for ($i = 0; $i < 14; $i++) {
            $window = $this->hours->on($cursor, $type);

            if ($window->open) {
                return [
                    'day' => $cursor->isSameDay($today->addDay()) ? 'i morgen' : $cursor->locale('nb')->dayName,
                    'opens' => str_ends_with($window->opens, ':00') ? substr($window->opens, 0, 2) : $window->opens,
                ];
            }

            $cursor = $cursor->addDay();
        }

        return null;
    }

    /**
     * Home: the next red day nationwide, today-relative. Far ones drop to a below-card line.
     *
     * @return array{0: ?array<string, mixed>, 1: ?array{slug: string, name: string, date: string}}
     */
    private function globalCard(CarbonImmutable $today): array
    {
        $date = $this->nextRedDay($today);

        if ($date === null) {
            return [null, null];
        }

        $ref = $this->reference($date);

        if ($date->lessThanOrEqualTo($today->addDays(self::AVVIK_WINDOW_DAYS))) {
            return [['zone' => 'action', 'label' => 'Neste røde dag', 'deadline' => $this->deadline($date), ...$ref], null];
        }

        return [['zone' => 'note', 'label' => 'Neste røde dag', 'note' => 'Ingen røde dager de neste 30 dagene'], $ref];
    }

    /**
     * Month pages: the month's next red day (in-card), or a no-red-days note plus
     * the nationwide next red day below — so the info is never hidden.
     *
     * @return array{0: array<string, mixed>, 1: ?array{slug: string, name: string, date: string}}
     */
    private function contextCard(CarbonImmutable $today, string $scope, ?CarbonImmutable $date): array
    {
        if ($date !== null) {
            return [['zone' => 'action', 'label' => "Neste røde dag i {$scope}", 'deadline' => $this->deadline($date), ...$this->reference($date)], null];
        }

        $next = $this->nextRedDay($today);

        return [
            ['zone' => 'note', 'label' => "Neste røde dag i {$scope}", 'note' => 'Ingen denne måneden'],
            $next !== null ? $this->reference($next) : null,
        ];
    }

    /** @return array{slug: string, name: string, date: string} */
    private function reference(CarbonImmutable $date): array
    {
        return [
            'slug' => (new DateSlug($date->day, $date->month))->toString(),
            'name' => ucfirst($this->classifier->name($date) ?? $date->locale('nb')->dayName),
            'date' => $date->locale('nb')->isoFormat('D. MMMM'),
        ];
    }

    private function deadline(CarbonImmutable $date): string
    {
        $beerLast = $this->hours->previousOpen($date, ProductType::Beer);
        $wineLast = $this->hours->previousOpen($date, ProductType::Wine);
        $binding = $beerLast->date->lessThan($wineLast->date) ? $beerLast->date : $wineLast->date;

        return $binding->locale('nb')->isoFormat('dddd D. MMMM');
    }

    private function nextRedDay(CarbonImmutable $today): ?CarbonImmutable
    {
        $cursor = $today->addDay();

        for ($i = 0; $i < 400; $i++, $cursor = $cursor->addDay()) {
            if ($this->classifier->isNamedHoliday($cursor) && ! $this->hours->on($cursor, ProductType::Beer)->open) {
                return $cursor;
            }
        }

        return null;
    }
}
