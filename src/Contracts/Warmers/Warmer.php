<?php

namespace WithCandour\StatamicToasty\Contracts\Warmers;

use Statamic\Sites\Site;
use WithCandour\StatamicToasty\Contracts\Models\ToastyUrl;
use WithCandour\StatamicToasty\Exceptions\WarmerException;

interface Warmer
{
    /**
     * Run the warmer.
     *
     * @param array|string $sites
     * @param array|null $urls
     * @param bool $output
     * @throws WarmerException
     * @return void
     */
    public function warm(mixed $sites = 'all', bool $output = false): void;

    /**
     * Determine whether toasty should crawl a given URL.
     *
     * @param string $url
     * @param Site $site
     * @return bool
     */
    public function shouldCrawl(string $url, Site $site): bool;

    /**
     * Get a toasty URL.
     *
     * @param \Statamic\Sites\Site $site
     * @param string $url
     * @return \WithCandour\StatamicToasty\Contracts\Models\ToastyUrl
     */
    public function makeUrl(Site $site, string $url): ToastyUrl;
}
