<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace User\Api\V1;

use Zend\Expressive\Hal\Metadata\RouteBasedResourceMetadata;
use Zend\Expressive\Hal\Metadata\RouteBasedCollectionMetadata;
use User\Api\V1\Entity\User;
use User\Api\V1\Hydrator\UserHydrator;
use User\Api\V1\Entity\Permission;
use User\Api\V1\Hydrator\UserPermissionHydrator;

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
            'doctrine' => $this->getDoctrine(),
            'Zend\Expressive\Hal\Metadata\MetadataMap' => $this->getMetadataMap()
        ];
    }

    public function getDoctrine(): array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'User\Api\V1\Entity' => 'user'
                    ]
                ],
                'user' => [
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
                Facade\UserFacade::class =>
                    Factory\Facade\UserFacadeFactory::class,
                Facade\PermissionFacade::class =>
                    Factory\Facade\PermissionFacadeFactory::class,
                Action\ListUserAction::class =>
                    Factory\Action\ListUserFactory::class,
                Action\ListUsernameAction::class =>
                    Factory\Action\ListUsernameFactory::class,
                Action\GetUserAction::class =>
                    Factory\Action\GetUserFactory::class,
                Action\CreateUserAction::class =>
                    Factory\Action\CreateUserFactory::class,
                Action\UpdateUserAction::class =>
                    Factory\Action\UpdateUserFactory::class,
                Action\GetUserPermissionAction::class =>
                    Factory\Action\GetUserPermissionFactory::class,
                Action\UpdateUserPermissionAction::class =>
                    Factory\Action\UpdateUserPermissionFactory::class,
                "UserValidationMiddleware" =>
                    Factory\Middleware\UserValidationMiddlewareFactory::class,
                "UserUpdateValidationMiddleware" =>
                    Factory\Middleware\UserUpdateValidationMiddlewareFactory::class,
                "PermissionValidationMiddleware" =>
                    Factory\Middleware\PermissionValidationMiddlewareFactory::class
            ]
        ];
    }

    /**
     * Returns metadata map for the HAL configurations
     *
     * @return array
     */
    public function getMetadataMap(): array
    {
        return [
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => Collection\UserCollection::class,
                'collection_relation' => 'users',
                'route' => 'api.v1.users.list'
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => User::class,
                'route' => 'api.v1.users.get',
                'extractor' => UserHydrator::class,
                'resource_identifier' => 'user_id',
                'route_identifier_placeholder' => 'id'
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Permission::class,
                'route' => 'api.v1.users.permissions.get',
                'extractor' => UserPermissionHydrator::class,
                'resource_identifier' => 'user_id',
                'route_identifier_placeholder' => 'id'
            ]
        ];
    }

    /**
     * Returns the route configuration
     *
     * @return array
     */
    public function getRoutes(): array
    {
        return [
            [
                'name' => 'api.v1.users.list',
                'path' => '/api/v1/users',
                'middleware' => [Action\ListUserAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.usernames.list',
                'path' => '/api/v1/users/usernames',
                'middleware' => [Action\ListUsernameAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.users.get',
                'path' => '/api/v1/users/:id',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\GetUserAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.users.create',
                'path' => '/api/v1/users',
                'middleware' => [
                    "UserValidationMiddleware", // validator
                    Action\CreateUserAction::class
                ],
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'api.v1.users.update',
                'path' => '/api/v1/users/:id',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [
                    "UserUpdateValidationMiddleware", // validator
                    Action\UpdateUserAction::class
                ],
                'allowed_methods' => ['PATCH', 'PUT']
            ],
            /**
             * Users' permissions routes
             */
            [
                'name' => 'api.v1.users.permissions.get',
                'path' => '/api/v1/users/:id/permissions',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\GetUserPermissionAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.users.permissions.update',
                'path' => '/api/v1/users/:id/permissions',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [
                    "PermissionValidationMiddleware",
                    Action\UpdateUserPermissionAction::class
                ],
                'allowed_methods' => ['PATCH', 'PUT']
            ]
        ];
    }
}
