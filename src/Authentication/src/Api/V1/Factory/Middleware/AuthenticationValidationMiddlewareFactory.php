<?php

/**
 * AuthenticationValidationMiddlewareFactory
 *
 * @package Authentication\Api\V1\Factory\Middleware
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace Authentication\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use App\Middleware\ValidationMiddleware;

class AuthenticationValidationMiddlewareFactory
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
                'name' => 'username',
                'required' => true,
                'validators' => [
                    [
                        'name' => \Zend\Validator\NotEmpty::class
                    ]
                ]
            ],
            [
                'name' => 'password',
                'required' => true,
                'validators' => [
                    [
                        'name' => \Zend\Validator\NotEmpty::class
                    ]
                ]
            ]
        ]);
    }
}
