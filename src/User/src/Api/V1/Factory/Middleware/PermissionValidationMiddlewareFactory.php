<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

/**
 * Description of PermissionValidationMiddlewareFactory
 */
class PermissionValidationMiddlewareFactory
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
                'name' => 'user_id',
                'required' => false,
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Digits::class
                    ]
                ]
            ],
            [
                'name' => 'manage_users',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\Boolean::class,
                        'options' => [
                            'casting' => false,
                            'type' => [
                                \Laminas\Filter\Boolean::TYPE_BOOLEAN,
                                \Laminas\Filter\Boolean::TYPE_INTEGER
                            ]
                        ]
                    ]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Callback::class,
                        'options' => [
                            'callback' => fn($value) => is_bool($value)
                        ]
                    ]
                ]
            ],
            [
                'name' => 'create_folders',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\Boolean::class,
                        'options' => [
                            'casting' => false,
                            'type' => [
                                \Laminas\Filter\Boolean::TYPE_BOOLEAN,
                                \Laminas\Filter\Boolean::TYPE_INTEGER
                            ]
                        ]
                    ]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Callback::class,
                        'options' => [
                            'callback' => fn($value) => is_bool($value)
                        ]
                    ]
                ]
            ],
            [
                'name' => 'access_all_folders',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\Boolean::class,
                        'options' => [
                            'casting' => false,
                            'type' => [
                                \Laminas\Filter\Boolean::TYPE_BOOLEAN,
                                \Laminas\Filter\Boolean::TYPE_INTEGER
                            ]
                        ]
                    ]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Callback::class,
                        'options' => [
                            'callback' => fn($value) => is_bool($value)
                        ]
                    ]
                ]
            ],
            [
                'name' => 'view_logs',
                'required' => false,
                'allow_empty' => true,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\Boolean::class,
                        'options' => [
                            'casting' => false,
                            'type' => [
                                \Laminas\Filter\Boolean::TYPE_BOOLEAN,
                                \Laminas\Filter\Boolean::TYPE_INTEGER
                            ]
                        ]
                    ]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Callback::class,
                        'options' => [
                            'callback' => fn($value) => is_bool($value)
                        ]
                    ]
                ]
            ]
        ]);
    }
}
