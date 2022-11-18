<?php

namespace WithCandour\StatamicToasty\Listeners;

class ToastySubscriber
{
    /**
     * Subscribe to content change events.
     *
     * @var array
     */
    protected $events = [
        \Statamic\Events\AssetSaved::class,
        \Statamic\Events\AssetDeleted::class,
        \Statamic\Events\EntrySaved::class,
        \Statamic\Events\EntryDeleted::class,
        \Statamic\Events\TermSaved::class,
        \Statamic\Events\TermDeleted::class,
        \Statamic\Events\GlobalSetSaved::class,
        \Statamic\Events\GlobalSetDeleted::class,
        \Statamic\Events\NavSaved::class,
        \Statamic\Events\NavDeleted::class,
        \Statamic\Events\FormSaved::class,
        \Statamic\Events\FormDeleted::class,
        \Statamic\Events\CollectionTreeSaved::class,
        \Statamic\Events\CollectionTreeDeleted::class,
        \Statamic\Events\NavTreeSaved::class,
        \Statamic\Events\NavTreeDeleted::class,
        \Statamic\Events\BlueprintSaved::class,
        \Statamic\Events\BlueprintDeleted::class,
    ];

    /**
     * @inheritDoc
     */
    public function subscribe($events)
    {
        foreach($this->events as $event) {
            $events->listen($event, ToastyListener::class . '@handle');
        }
    }
}
