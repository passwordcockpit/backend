<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

/**
 * Description of FolderUserValidationMiddleware
 */
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
                        'name' => \Zend\Validator\Digits::class
                    ],
                    [
                        'name' => \Zend\Validator\Between::class,
                        'options' => [
                            'min' => 1,
                            'max' => 2
                        ]
                    ]
                ]
            ]
        ]);
    }
}
