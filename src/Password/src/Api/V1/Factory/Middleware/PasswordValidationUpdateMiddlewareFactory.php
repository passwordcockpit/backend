<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Middleware;

use Psr\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

/**
 * Description of PasswordValidationPatchMiddlewareFactory
 */
class PasswordValidationUpdateMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ValidationMiddleware([
            [
                'name' => 'folder_id',
                'required' => false,
                'validators' => [
                    [
                        'name' => \Zend\Validator\Digits::class
                    ]
                ]
            ],
            [
                'name' => 'title',
                'required' => false,
                'continue_if_empty' => true,
                'filters' => [
                    ['name' => \Zend\Filter\StringTrim::class],
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Zend\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Zend\Validator\StringLength::class,
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
                    ['name' => \Zend\Filter\StringTrim::class],
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Zend\Validator\NotEmpty::class
                    ],
                    [
                        'name' => \Zend\Validator\StringLength::class,
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
                        'name' => \Zend\Filter\StripTags::class,
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
                    ['name' => \Zend\Filter\StringTrim::class]
                ],
                'validators' => [
                    [
                        'name' => \Zend\Validator\StringLength::class,
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
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StringTrim::class],
                    ['name' => \Zend\Filter\StripNewlines::class]
                ],
                'validators' => [
                    ['name' => \Zend\Validator\NotEmpty::class],
                    [
                        'name' => \Zend\Validator\StringLength::class,
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
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StringTrim::class],
                    ['name' => \Zend\Filter\StripNewlines::class]
                ],
                'validators' => [
                    ['name' => \Zend\Validator\NotEmpty::class],
                    [
                        'name' => \Zend\Validator\StringLength::class,
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
                    ['name' => \Zend\Filter\StringTrim::class],
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => 100
                        ]
                    ],
                    [
                        'name' => \Zend\Validator\Uri::class,
                        'options' => [
                            'allowRelative' => false,
                            'allowAbsolute' => true
                        ]
                    ]
                ]
            ],
            [
                'name' => 'tags',
                'required' => false,
                'filters' => [
                    ['name' => \Zend\Filter\StripTags::class],
                    ['name' => \Zend\Filter\StripNewlines::class]
                ],
                'validators' => [
                    [
                        'name' => \Zend\Validator\StringLength::class,
                        'options' => [
                            'max' => 400
                        ]
                    ]
                ]
            ]
        ]);
    }
}
