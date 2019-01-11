<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

/**
 * Description of FolderValidationMiddlewareFactory
 */
class FolderValidationMiddlewareFactory
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
                'name' => 'name',
                'required' => true,
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
                            'max' => 45
                        ]
                    ]
                ]
            ],
            [
                'name' => 'parent_id',
                'required' => false,
                'validators' => [
                    [
                        'name' => \Zend\Validator\Digits::class
                    ]
                ]
            ]
        ]);
    }
}
