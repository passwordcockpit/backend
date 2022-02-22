<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1;

use Folder\Api\V1\Entity\Folder;
use Folder\Api\V1\Entity\FolderUser;
use Folder\Api\V1\Hydrator\FolderHydrator;
use Folder\Api\V1\Hydrator\FolderHalHydrator;
use Folder\Api\V1\Hydrator\FolderUserHydrator;
use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;

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
    public function __invoke() : array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'routes' => $this->getRoutes(),
            'doctrine' => $this->getDoctrine(),
            \Mezzio\Hal\Metadata\MetadataMap::class => $this->getMetadataMap()
        ];
    }

    /**
     * Returns the doctrine orm configuration
     *
     * @return array
     */

    public function getDoctrine() : array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Folder\Api\V1\Entity' => 'folder'
                    ]
                ],
                'folder' => [
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
    public function getDependencies() : array
    {
        return [
            'invokables' => [],
            'factories' => [
                Facade\FolderFacade::class =>
                    Factory\Facade\FolderFacadeFactory::class,
                Facade\FolderUserFacade::class =>
                    Factory\Facade\FolderUserFacadeFactory::class,
                Action\ListFolderAction::class =>
                    Factory\Action\ListFolderFactory::class,
                Action\GetFolderAction::class =>
                    Factory\Action\GetFolderFactory::class,
                Action\CreateFolderAction::class =>
                    Factory\Action\CreateFolderFactory::class,
                Action\DeleteFolderAction::class =>
                    Factory\Action\DeleteFolderFactory::class,
                Action\UpdateFolderAction::class =>
                    Factory\Action\UpdateFolderFactory::class,
                Action\ListFolderPasswordAction::class =>
                    Factory\Action\ListFolderPasswordFactory::class,
                Action\ListFolderUserAction::class =>
                    Factory\Action\ListFolderUserFactory::class,
                Action\GetFolderUserAction::class =>
                    Factory\Action\GetFolderUserFactory::class,
                Action\AddFolderUserAction::class =>
                    Factory\Action\AddFolderUserFactory::class,
                Action\UpdateFolderUserAction::class =>
                    Factory\Action\UpdateFolderUserFactory::class,
                Action\DeleteFolderUserAction::class =>
                    Factory\Action\DeleteFolderUserFactory::class,
                "FolderValidationMiddleware" =>
                    Factory\Middleware\FolderValidationMiddlewareFactory::class,
                "FolderUserValidationMiddleware" =>
                    Factory\Middleware\FolderUserValidationMiddlewareFactory::class
            ]
        ];
    }

    /**
     * Returns metadata map for the HAL configurations
     *
     * @return array
     */

    public function getMetadataMap() : array
    {
        return [
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => Collection\FolderCollection::class,
                'collection_relation' => 'folders',
                'route' => 'api.v1.folders.list'
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Folder::class,
                'route' => 'api.v1.folders.get',
                'extractor' => FolderHalHydrator::class,
                'resource_identifier' => 'folder_id',
                'identifiers_to_placeholders_mapping' => [
                  'folder_id' => 'id',
                ],
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => FolderUser::class,
                'route' => 'api.v1.folders.users.get',
                'extractor' => FolderUserHydrator::class,
                'resource_identifier' => 'folder_user_id'
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
                'name' => 'api.v1.folders.list',
                'path' => '/api/v1/folders',
                'middleware' => [
                    \App\Validator\StringParameterValidator::class,
                    Action\ListFolderAction::class
                ],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.folders.delete',
                'path' => '/api/v1/folders/:id',
                'middleware' => [Action\DeleteFolderAction::class],
                'allowed_methods' => ['DELETE']
            ],
            [
                'name' => 'api.v1.folders.get',
                'path' => '/api/v1/folders/:id',
                'options' => [
                    'constraints' => ['id' => '\d+'] // controllo che id sia solo numerico
                ],
                'middleware' => [Action\GetFolderAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.folders.create',
                'path' => '/api/v1/folders',
                'middleware' => [
                    "FolderValidationMiddleware", // validator
                    Action\CreateFolderAction::class
                ],
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'api.v1.folders.update',
                'path' => '/api/v1/folders/:id',
                'options' => [
                    'constraints' => ['id' => '\d+'] // controllo che id sia solo numerico
                ],
                'middleware' => [
                    "FolderValidationMiddleware", // validator
                    Action\UpdateFolderAction::class
                ],
                'allowed_methods' => ['PATCH', 'PUT']
            ],
            [
                'name' => 'api.v1.folders.users.list',
                'path' => '/api/v1/folders/:id/users',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\ListFolderUserAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.folders.passwords.list',
                'path' => '/api/v1/folders/:id/passwords',
                'options' => [
                    'constraints' => ['id' => '\d+']
                ],
                'middleware' => [Action\ListFolderPasswordAction::class],
                'allowed_methods' => ['GET']
            ],

            [
                'name' => 'api.v1.folders.users.add',
                'path' => '/api/v1/folders/:folderId/users/:userId',
                'options' => [
                    'constraints' => ['folderId' => '\d+'],
                    'constraints' => ['userId' => '\d+']
                ],
                'middleware' => [
                    "FolderUserValidationMiddleware", // validator
                    Action\AddFolderUserAction::class
                ],
                'allowed_methods' => ['POST']
            ],
            [
                'name' => 'api.v1.folders.users.update',
                'path' => '/api/v1/folders/:folderId/users/:userId',
                'options' => [
                    'constraints' => ['folderId' => '\d+'],
                    'constraints' => ['userId' => '\d+']
                ],
                'middleware' => [
                    "FolderUserValidationMiddleware", // validator
                    Action\UpdateFolderUserAction::class
                ],
                'allowed_methods' => ['PATCH', 'PUT']
            ],
            [
                'name' => 'api.v1.folders.users.delete',
                'path' => '/api/v1/folders/:folderId/users/:userId',
                'options' => [
                    'constraints' => ['folderId' => '\d+'],
                    'constraints' => ['userId' => '\d+']
                ],
                'middleware' => [Action\DeleteFolderUserAction::class],
                'allowed_methods' => ['DELETE']
            ],
            [
                'name' => 'api.v1.folders.users.get',
                'path' => '/api/v1/folders/:folderId/users/:userId',
                'options' => [
                    'constraints' => ['folderId' => '\d+'],
                    'constraints' => ['userId' => '\d+']
                ],
                'middleware' => [Action\GetFolderUserAction::class],
                'allowed_methods' => ['GET']
            ]
        ];
    }
}
