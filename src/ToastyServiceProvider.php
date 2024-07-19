<?php

namespace WithCandour\StatamicToasty;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Cache;
use Statamic\Providers\AddonServiceProvider;
use WithCandour\StatamicToasty\Builders\ToastyUrl as ToastyUrlBuilder;
use WithCandour\StatamicToasty\Commands\ToastyWarmCommand;
use WithCandour\StatamicToasty\Contracts\Builders\ToastyUrl as ToastyUrlBuilderContract;
use WithCandour\StatamicToasty\Contracts\Toasty as ToastyContract;
use WithCandour\StatamicToasty\Contracts\Warmers\Warmer;
use WithCandour\StatamicToasty\Jobs\ToastyWarm;
use WithCandour\StatamicToasty\Listeners\ToastySubscriber;
use WithCandour\StatamicToasty\Warmers\DefaultWarmer;

class ToastyServiceProvider extends AddonServiceProvider
{
    /**
     * @inheritDoc
     */
    public $singletons = [
        ToastyContract::class => Toasty::class,
    ];

    /**
     * @inheritDoc
     */
    public $bindings = [
        ToastyUrlBuilderContract::class => ToastyUrlBuilder::class,
        Warmer::class => DefaultWarmer::class,
    ];

    /**
     * @inheritDoc
     */
    protected $commands = [
        ToastyWarmCommand::class,
    ];

    /**
     * @inheritDoc
     */
    protected $subscribe = [
        ToastySubscriber::class,
    ];

    /**
     * @inheritDoc
     */
    public function boot()
    {
        parent::boot();

        $this->mergeConfigFrom(__DIR__ . '/../config/toasty.php', 'statamic.toasty');

        $this->publishes([
            __DIR__ . '/../config/toasty.php' => config_path('statamic/toasty.php'),
        ], 'toasty-config');
    }

    /**
     * @inheritDoc
     */
    protected function schedule($schedule)
    {
        if (\config('statamic.toasty.enabled')) {
            $schedule
                ->job(new ToastyWarm('all', false))
                ->name('toasty-warm')
                ->everyMinute()
                ->withoutOverlapping()
                ->when(function () {
                    return Cache::get('toasty.invalid') === true;
                });
        }
    }
}
