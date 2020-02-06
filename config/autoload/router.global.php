<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

use Mezzio\Router\RouterInterface;
use Mezzio\Router\LaminasRouter;

return [
    'dependencies' => [
        'invokables' => [
            RouterInterface::class => LaminasRouter::class
        ]
    ]
];
