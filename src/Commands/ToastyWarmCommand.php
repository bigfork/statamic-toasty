<?php

namespace WithCandour\StatamicToasty\Commands;

use Illuminate\Console\Command;
use Statamic\Facades\Site;
use WithCandour\StatamicToasty\Jobs\ToastyWarm;

class ToastyWarmCommand extends Command
{
    /**
     * @inheritDoc
     */
    protected $signature = 'toasty:warm {site=all}';

    /**
     * @inheritDoc
     */
    protected $description = 'Run the toasty warmer';

    /**
     * Execute the warmer command.
     *
     * @return mixed
     */
    public function handle()
    {
        $site = $this->argument('site');

        if (!(bool)config('statamic.static_caching.strategy', null)) {
            return $this->error("Static caching not enabled, warming will have no effect");
        }

        if (!\config('statamic.toasty.enabled')) {
            return $this->error("Toasty not enabled");
        }

        if ($site !== 'all') {
            $statamicSite = Site::get($site);

            if (!$statamicSite) {
                return $this->error("No site found with the handle \"{$site}\"");
            }
        }

        ToastyWarm::dispatchSync($site, true);
    }
}
