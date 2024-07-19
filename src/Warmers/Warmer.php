<?php

namespace WithCandour\StatamicToasty\Warmers;

use Illuminate\Support\Facades\App;
use Statamic\Facades\Pattern;
use Statamic\Sites\Site;
use Statamic\Support\Str;
use WithCandour\StatamicToasty\Contracts\Builders\ToastyUrl as ToastyUrlBuilder;
use WithCandour\StatamicToasty\Contracts\Models\ToastyUrl;
use WithCandour\StatamicToasty\Contracts\Warmers\Warmer as Contract;

abstract class Warmer implements Contract
{
    /**
     * @inheritDoc
     */
    public function shouldCrawl(string $url, Site $site): bool
    {
        if (Str::contains($url, '#')) {
            return false;
        }

        // TODO: Ensure we're not loading linked local assets

        return Pattern::startsWith(
            Str::ensureRight($url, '/'),
            $site->absoluteUrl()
        );
    }

    /**
     * @inheritDoc
     */
    public function makeUrl(Site $site, string $url): ToastyUrl
    {
        /**
         * @var \WithCandour\StatamicToasty\Builders\ToastyUrl
         */
        $builder = App::make(ToastyUrlBuilder::class);

        return $builder
            ->site($site)
            ->url($url)
            ->build();
    }

    /**
     * Output a line.
     *
     * @param string $value
     * @return void
     */
    protected function output(string $value)
    {
        echo $value . PHP_EOL;
    }
}
