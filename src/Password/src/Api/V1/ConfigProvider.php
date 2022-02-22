<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1;

use Password\Api\V1\Entity\Password;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;
use Password\Api\V1\Hydrator\PasswordHydrator;

/**
 * Description of ConfigProvider
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
            \Mezzio\Hal\Metadata\MetadataMap::class => $this->getMetadataMap()
        ];
    }

    public function getDoctrine(): array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Password\Api\V1\Entity' => 'password'
                    ]
                ],
                'password' => [
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
                Action\ListPasswordAction::class =>
                    Factory\Action\ListPasswordFactory::class,
                Action\GetPasswordAction::class =>
                    Factory\Action\GetPasswordFactory::class,
                Action\CreatePasswordAction::class =>
                    Factory\Action\CreatePasswordFactory::class,
                Action\UpdatePasswordAction::class =>
                    Factory\Action\UpdatePasswordFactory::class,
                Action\MovePasswordAction::class =>
                    Factory\Action\MovePasswordFactory::class,
                Facade\PasswordFacade::class =>
                    Factory\Facade\PasswordFacadeFactory::class,
                Action\DeletePasswordAction::class =>
                    Factory\Action\DeletePasswordFactory::class,
                "PasswordValidationCreateMiddleware" =>
                    Factory\Middleware\PasswordValidationCreateMiddlewareFactory::class,
                "PasswordValidationUpdateMiddleware" =>
                    Factory\Middleware\PasswordValidationUpdateMiddlewareFactory::class,
                Action\ListPasswordFilesAction::class =>
                    Factory\Action\ListPasswordFilesFactory::class
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
                'collection_class' => Collection\PasswordCollection::class,
                'collection_relation' => 'passwords',
                'route' => 'api.v1.passwords.list'
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Password::class,
                'route' => 'api.v1.passwords.get',
                'extractor' => PasswordHydrator::class,
                'resource_identifier' => 'password_id',
                'identifiers_to_placeholders_mapping' => [
                  'password_id' => 'id',
                ],
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
                'name' => 'api.v1.passwords.list',
                'path' => '/api/v1/passwords',
                'middleware' => [
                    \App\Validator\StringParameterValidator::class,
                    Action\ListPasswordAction::class
                ],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.passwords.get',
                'path' => '/api/v1/passwords/:id',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\GetPasswordAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.passwords.create',
                'path' => '/api/v1/passwords',
                'middleware' => [
                    "PasswordValidationCreateMiddleware",
                    Action\CreatePasswordAction::class
                ],
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'api.v1.passwords.move',
                'path' => '/api/v1/passwords',
                'middleware' => [Action\MovePasswordAction::class],
                'allowed_methods' => ['PATCH']
            ],
            [
                'name' => 'api.v1.passwords.update',
                'path' => '/api/v1/passwords/:id',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [
                    "PasswordValidationUpdateMiddleware",
                    Action\UpdatePasswordAction::class
                ],
                'allowed_methods' => ['PATCH', 'PUT']
            ],
            [
                'name' => 'api.v1.passwords.delete',
                'path' => '/api/v1/passwords/:id',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\DeletePasswordAction::class],
                'allowed_methods' => ['DELETE']
            ],
            [
                'name' => 'api.v1.passwords.files.list',
                'path' => '/api/v1/passwords/:id/files',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\ListPasswordFilesAction::class],
                'allowed_methods' => ['GET']
            ]
        ];
    }
}
