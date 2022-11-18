<?php

namespace WithCandour\StatamicToasty\Contracts\Builders;

use Statamic\Sites\Site;
use WithCandour\StatamicToasty\Contracts\Models\ToastyUrl as ToastyUrlModel;

interface ToastyUrl
{
    /**
     * Set the site for this url.
     *
     * @param \Statamic\Sites\Site $value
     * @return self
     */
    public function site(Site $value): self;

    /**
     * Set the url string for this url.
     *
     * @param string $value
     */
    public function url(string $value): self;

    /**
     * Build this url and return an model representation.
     *
     * @return \WithCandour\StatamicToasty\Contracts\Models\ToastyUrl
     */
    public function build(): ToastyUrlModel;
}
