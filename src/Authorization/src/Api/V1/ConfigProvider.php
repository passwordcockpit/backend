<?php

/**
 * ConfigProvider
 *
 * @package Authorization\Api\V1
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace Authorization\Api\V1;

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
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'routes' => $this->getRoutes(),
            'doctrine' => $this->getDoctrine()
        ];
    }

    public function getDoctrine(): array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Authorization\Api\V1\Entity' => 'tokenuser'
                    ]
                ],
                'tokenuser' => [
                    'class' =>
                        \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => __DIR__ . '/Entity'
                ]
            ]
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies(): array
    {
        return [
            'invokables' => [],
            'factories' => [
                Middleware\AuthorizationMiddleware::class =>
                    Factory\Middleware\AuthorizationMiddlewareFactory::class,
                Action\AuthorizationUpdateToken::class =>
                    Factory\Action\AuthorizationUpdateTokenFactory::class,
                AssertionPluginManager::class =>
                    Factory\Middleware\AssertionPluginManagerFactory::class,
                Facade\TokenUserFacade::class =>
                    Factory\Facade\TokenUserFacadeFactory::class,
                Action\AuthorizationLogout::class =>
                    Factory\Action\AuthorizationLogoutFactory::class
            ]
        ];
    }

    /**
     * Return the route configuration
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            [
                'name' => 'api.v1.authorization.update',
                'path' => '/api/auth/update',
                'middleware' => [Action\AuthorizationUpdateToken::class],
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'api.v1.authorization.logout',
                'path' => '/api/v1/logout',
                'middleware' => [Action\AuthorizationLogout::class],
                'allowed_methods' => ['DELETE']
            ]
        ];
    }
}
