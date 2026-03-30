<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Middleware;

use Psr\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

class FolderUserValidationMiddlewareFactory
{
    /**
     * Invoke method
     *
     * @param ContainerInterface $container
     * @return ValidationMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ValidationMiddleware([
            [
                'name' => 'access',
                'required' => true,
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Digits::class
                    ],
                    [
                        'name'    => \Laminas\Validator\NumberComparison::class,
                        'options' => [
                            'min'          => 1,
                            'max'          => 2,
                            'inclusiveMin' => true,
                            'inclusiveMax' => true,
                        ],
                    ]
                ]
            ]
        ]);
    }
}
