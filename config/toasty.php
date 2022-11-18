<?php

return [

    /**
     * Get whether toasty is enabled.
     */
    'enabled' => \env('TOASTY_ENABLED', false),

    /**
     * Get a list of sites which should be warmed
     */
    'sites' => [
        'default'
    ],

    /**
     * Get a list of query parameters which should be crawled.
     */
    'carwlable_query_parameters' => [
        'page'
    ],

];
