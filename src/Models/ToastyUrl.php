<?php

namespace WithCandour\StatamicToasty\Models;

use Statamic\Sites\Site;
use WithCandour\StatamicToasty\Contracts\Models\ToastyUrl as Contract;

class ToastyUrl implements Contract
{
    /**
     * @var \Statamic\Sites\Site|null
     */
    protected ?Site $site = null;

    /**
     * @var string|null
     */
    protected ?string $url = null;

    /**
     * @param \Statamic\Sites\Site $site
     * @param string $url
     */
    public function __construct(Site $site, string $url)
    {
        $this->site = $site;
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function site(): Site
    {
        return $this->site;
    }

    /**
     * @inheritDoc
     */
    public function url(): string
    {
        return $this->url;
    }
}
