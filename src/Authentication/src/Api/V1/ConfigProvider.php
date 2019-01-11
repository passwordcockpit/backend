<?php

/**
 * ConfigProvider
 *
 * @package Authentication\Api\V1
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace Authentication\Api\V1;

use Slim\Middleware\JwtAuthentication;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'routes' => $this->getRoutes()
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies() : array
    {
        return [
            'invokables' => [],
            'factories' => [
                Middleware\AuthenticationMiddleware::class =>
                    Factory\Middleware\AuthenticationMiddlewareFactory::class,
                JwtAuthentication::class =>
                    Factory\Middleware\JwtAuthenticationFactory::class,
                Action\AuthenticationCreateAction::class =>
                    Factory\Action\AuthenticationCreateFactory::class,
                "AuthenticationValidationMiddleware" =>
                    Factory\Middleware\AuthenticationValidationMiddlewareFactory::class
            ]
        ];
    }

    /**
     * Return the route configuration
     *
     * @return array
     */
    public function getRoutes() : array
    {
        return [
            [
                'name' => 'api.v1.authentication.create',
                'path' => '/api/auth',
                'middleware' => [
                    "AuthenticationValidationMiddleware",
                    Action\AuthenticationCreateAction::class
                ],
                'allowed_methods' => ['POST']
            ]
        ];
    }
}
