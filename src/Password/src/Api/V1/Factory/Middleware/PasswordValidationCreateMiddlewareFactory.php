<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Middleware;

use Psr\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

/**
 * Description of PasswordValidationPostMiddlewareFactory
 */
class PasswordValidationCreateMiddlewareFactory
{
    /**
     * Invoke method, create instance of ValidationMiddleware class
     *
     * @param ContainerInterface $container
     * @return ValidationMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ValidationMiddleware([
            [
                'name' => 'folder_id',
                'required' => true,
                'validators' => [
                    [
                        'name' => \Laminas\Validator\Digits::class
                    ]
                ]
            ],
            [
                'name' => 'title',
                'required' => true,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'min' => 2,
                            'max' => 100
                        ]
                    ]
                ]
            ],
            [
                'name' => 'icon',
                'required' => false,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 45
                        ]
                    ]
                ]
            ],
            [
                'name' => 'description',
                'required' => false,
                'filters' => [
                    [
                        'name' => \Laminas\Filter\StripTags::class,
                        'options' => [
                            'allowTags' => [
                                'p',
                                'strong',
                                'em',
                                'u',
                                'ul',
                                'ol',
                                'li'
                            ]
                        ]
                    ],
                    ['name' => \Laminas\Filter\StringTrim::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 4000
                        ]
                    ]
                ]
            ],
            [
                'name' => 'username',
                'required' => false,
                'filters' => [
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    ['name' => \Laminas\Validator\NotEmpty::class],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 45
                        ]
                    ]
                ]
            ],
            [
                'name' => 'password',
                'required' => false,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    ['name' => \Laminas\Validator\NotEmpty::class],
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 500
                        ]
                    ]
                ]
            ],
            [
                'name' => 'url',
                'required' => false,
                'filters' => [
                    ['name' => \Laminas\Filter\StringTrim::class],
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 100
                        ]
                    ]
                ]
            ],
            [
                'name' => 'tags',
                'required' => false,
                'filters' => [
                    ['name' => \Laminas\Filter\StripTags::class],
                    ['name' => \Laminas\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Laminas\Validator\StringLength::class,
                        'options' => [
                            'max' => 400
                        ]
                    ]
                ]
            ]
        ]);
    }
}
