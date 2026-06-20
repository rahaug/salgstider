<?php

namespace App\Calendar;

use App\Calendar\Rules\ForbiddenSalesDay;
use App\Calendar\Rules\NormalDay;
use App\Calendar\Rules\ReducedEve;
use App\Calendar\Rules\SalesRule;
use App\Calendar\Rules\StoreClosingEve;
use App\Calendar\Rules\WineClosedOnChristmasEve;
use App\Enums\Band;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class OpeningHours
{
    /** @var list<SalesRule> */
    private array $rules;

    public function __construct(private DayClassifier $classifier)
    {
        $this->rules = [
            new ForbiddenSalesDay($this->classifier),
            new WineClosedOnChristmasEve($this->classifier),
            new StoreClosingEve($this->classifier),
            new ReducedEve($this->classifier),
            new NormalDay,
        ];
    }

    public function on(CarbonImmutable $date, ProductType $type): SalesWindow
    {
        foreach ($this->rules as $rule) {
            if ($rule->appliesTo($date, $type)) {
                return $this->toWindow($date, $type, $rule);
            }
        }

        throw new \LogicException('No sales rule matched; NormalDay should always apply.');
    }

    public function previousOpen(CarbonImmutable $date, ProductType $type): ?LastSale
    {
        $cursor = $date->subDay();

        for ($i = 0; $i < 14; $i++) {
            $window = $this->on($cursor, $type);

            if ($window->open) {
                return new LastSale($cursor, $window->closes);
            }

            $cursor = $cursor->subDay();
        }

        return null;
    }

    public function deadlineBefore(CarbonImmutable $date, ProductType $type): ?string
    {
        if (! $this->on($date, $type)->open) {
            return null;
        }

        $cursor = $date->addDay();

        for ($i = 0; $i < 14; $i++) {
            if ($this->on($cursor, $type)->open) {
                return null;
            }

            if ($this->classifier->isNamedHoliday($cursor)
                || ($type === ProductType::Wine && $this->classifier->isChristmasEve($cursor))) {
                return $this->classifier->name($cursor);
            }

            $cursor = $cursor->addDay();
        }

        return null;
    }

    private function toWindow(CarbonImmutable $date, ProductType $type, SalesRule $rule): SalesWindow
    {
        $band = $rule->band($date);

        if ($band === Band::Closed) {
            return new SalesWindow(open: false, reason: $rule->label($date));
        }

        [$opens, $closes] = match ($type) {
            ProductType::Beer => match ($band) {
                Band::EarlyClose => ['08:00', '16:00'],
                Band::Reduced => ['08:00', '18:00'],
                default => ['08:00', '20:00'],
            },
            ProductType::Wine => match ($band) {
                Band::EarlyClose, Band::Reduced => ['10:00', '16:00'],
                default => ['10:00', '18:00'],
            },
        };

        return new SalesWindow(open: true, opens: $opens, closes: $closes);
    }
}
