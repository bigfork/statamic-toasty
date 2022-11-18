<?php

namespace WithCandour\StatamicToasty\Builders;

use Statamic\Sites\Site;
use WithCandour\StatamicToasty\Contracts\Builders\ToastyUrl as Contract;
use WithCandour\StatamicToasty\Contracts\Models\ToastyUrl as ToastyUrlModelContract;
use WithCandour\StatamicToasty\Models\ToastyUrl as ToastyUrlModel;

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
     * @inheritDoc
     */
    public function site(Site $value): Contract
    {
        $this->site = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function url(string $value): Contract
    {
        $this->url = $value;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function build(): ToastyUrlModelContract
    {
        return new ToastyUrlModel($this->site, $this->url);
    }
}
