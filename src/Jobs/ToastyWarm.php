<?php

namespace WithCandour\StatamicToasty\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUniqueUntilProcessing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\App;
use WithCandour\StatamicToasty\Contracts\Toasty;

class ToastyWarm implements ShouldQueue, ShouldBeUniqueUntilProcessing
{
    use Dispatchable, InteractsWithQueue, Queueable;

    /**
     * @var \WithCandour\StatamicToasty\Contracts\Toasty|null
     */
    protected ?Toasty $toasty = null;

    /**
     * @var string|array
     */
    protected mixed $sites = 'all';

    /**
     * @var bool
     */
    protected bool $output = false;

    /**
     * @param string|array $sites
     * @param array|null $urls
     * @param bool $output
     */
    public function __construct(mixed $sites = 'all', bool $output = false)
    {
        $this->sites = $sites;
        $this->output = $output;
    }

    /**
     * Get an instance of the toasty class.
     *
     * @return \WithCandour\StatamicToasty\Contracts\Toasty
     */
    protected function toasty(): Toasty
    {
        if (!$this->toasty) {
            $this->toasty = App::make(Toasty::class);
        }

        return $this->toasty;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(): void
    {
        $this
            ->toasty()
            ->warmer()
            ->warm($this->sites, $this->output);
    }
}
