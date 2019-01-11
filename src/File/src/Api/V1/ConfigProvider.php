<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace File\Api\V1;

use File\Api\V1\Entity\File;
use File\Api\V1\Hydrator\FileHydrator;
use Zend\Expressive\Hal\Metadata\RouteBasedResourceMetadata;
use Zend\Expressive\Hal\Metadata\RouteBasedCollectionMetadata;

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
            'Zend\Expressive\Hal\Metadata\MetadataMap' => $this->getMetadataMap()
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
                        'File\Api\V1\Entity' => 'file'
                    ]
                ],
                'file' => [
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
                Facade\FileFacade::class =>
                    Factory\Facade\FileFacadeFactory::class,
                Hydrator\FileHydrator::class =>
                    Factory\Hydrator\FileHydratorFactory::class,
                Action\GetFileAction::class =>
                    Factory\Action\GetFileFactory::class,
                Action\DownloadFileAction::class =>
                    Factory\Action\DownloadFileFactory::class,
                Action\ListFileAction::class =>
                    Factory\Action\ListFileFactory::class,
                Action\DeleteFileAction::class =>
                    Factory\Action\DeleteFileActionFactory::class,
                Action\UpdateFileAction::class =>
                    Factory\Action\UpdateFileActionFactory::class
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
                'collection_class' => Collection\FileCollection::class,
                'collection_relation' => 'files',
                'route' => 'api.v1.files.list'
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => File::class,
                'route' => 'api.v1.files.get',
                'extractor' => FileHydrator::class,
                'resource_identifier' => 'file_id',
                'route_identifier_placeholder' => 'id'
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
                'name' => 'api.v1.files.get',
                'path' => '/api/v1/files/:id',
                'middleware' => Action\GetFileAction::class,
                'allowed_methods' => ['GET'],
                'options' => [
                    'constraints' => ['id' => '\d+']
                ]
            ],
            [
                'name' => 'api.v1.upload.files.get',
                'path' => '/api/v1/upload/files/:id',
                'middleware' => Action\DownloadFileAction::class,
                'allowed_methods' => ['GET'],
                'options' => [
                    'constraints' => ['id' => '\d+']
                ]
            ],
            [
                'name' => 'api.v1.files.list',
                'path' => '/api/v1/files',
                'middleware' => [Action\ListFileAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.files.delete',
                'path' => '/api/v1/files/:id',
                'middleware' => Action\DeleteFileAction::class,
                'allowed_methods' => ['DELETE'],
                'options' => [
                    'constraints' => ['id' => '\d+']
                ]
            ],
            [
                'name' => 'api.v1.files.update',
                'path' => '/api/v1/passwords/:id/files',
                'middleware' => Action\UpdateFileAction::class,
                'allowed_methods' => ['POST'],
                'options' => [
                    'constraints' => ['id' => '\d+']
                ]
            ]
        ];
    }
}
