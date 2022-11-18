<?php

namespace WithCandour\StatamicToasty\Contracts\Models;

use Statamic\Sites\Site;

interface ToastyUrl
{
    /**
     * Get the site for this url.
     *
     * @return \Statamic\Sites\Site
     */
    public function site(): Site;

    /**
     * Get the url string for this url.
     *
     * @return string
     */
    public function url(): string;
}
