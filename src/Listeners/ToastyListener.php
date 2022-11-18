<?php

namespace WithCandour\StatamicToasty\Listeners;

use Illuminate\Support\Facades\Cache;
use Statamic\Events\Event;

class ToastyListener
{
    /**
     * @inheritDoc
     */
    public function handle(Event $event)
    {
        Cache::put('toasty.invalid', true);
    }
}
