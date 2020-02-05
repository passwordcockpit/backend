<?php

use Laminas\ConfigAggregator\ConfigAggregator;

return [
    // Toggle the configuration cache. Set this to boolean false, or remove the
    // directive, to disable configuration caching. Toggling development mode
    // will also disable it by default; clear the configuration cache using
    // `composer clear-config-cache`.
    ConfigAggregator::ENABLE_CACHE => false,

    // Enable debugging; typically used to provide debugging information within templates.
    'debug' => false,

    'mezzio' => [
        // Enable programmatic pipeline: Any `middleware_pipeline` or `routes`
        // configuration will be ignored when creating the `Application` instance.
        'programmatic_pipeline' => true
    ],
];
