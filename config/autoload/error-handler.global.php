<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

use App\Service\ErrorResponseGeneratorFactory;

return [
    'error_handler' => [
        'default_content_type' => 'application/json',
        'plugins' => [
            'factories' => [
                'application/json' => ErrorResponseGeneratorFactory::class
            ]
        ]
    ]
];
