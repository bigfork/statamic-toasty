<?php

namespace WithCandour\StatamicToasty;

use Illuminate\Support\Facades\Event;
use WithCandour\StatamicToasty\Contracts\Toasty as Contract;
use WithCandour\StatamicToasty\Contracts\Warmers\Warmer;
use WithCandour\StatamicToasty\Listeners\ToastyListener;

class Toasty implements Contract
{
    /**
     * @var \WithCandour\StatamicToasty\Contracts\Warmers\Warmer|null
     */
    protected ?Warmer $warmer = null;

    /**
     * @param \WithCandour\StatamicToasty\Contracts\Warmers\Warmer $warmer
     */
    public function __construct(Warmer $warmer)
    {
        $this->warmer = $warmer;
    }

    /**
     * @inheritDoc
     */
    public function listen(string $event): void
    {
        Event::listen($event, ToastyListener::class . '@handle');
    }

    /**
     * @inheritDoc
     */
    public function warmer(): ?Warmer
    {
        return $this->warmer;
    }

    /**
     * Determine whether static caching is enabled on the site.
     *
     * @return bool
     */
    protected function staticCachingEnabled(): bool
    {
        return (bool)config('statamic.static_caching.strategy', null);
    }

    /**
     * Determine whether toasty is enabled on the site.
     *
     * @return bool
     */
    protected function toastyEnabled(): bool
    {
        return (bool)\config('statamic.toasty.enabled', false);
    }
}
