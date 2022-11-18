<?php

namespace WithCandour\StatamicToasty\Contracts;

use WithCandour\StatamicToasty\Contracts\Warmers\Warmer;

interface Toasty
{
    /**
     * Listen to a custom event.
     *
     * @param string
     * @return void
     */
    public function listen(string $event): void;

    /**
     * Get the warmer for the site.
     *
     * @return \WithCandour\StatamicToasty\Contracts\Warmers\Warmer|null
     */
    public function warmer(): ?Warmer;
}
