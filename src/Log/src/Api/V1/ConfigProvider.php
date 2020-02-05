<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1;

use Mezzio\Hal\Metadata\RouteBasedResourceMetadata;
use Mezzio\Hal\Metadata\RouteBasedCollectionMetadata;
use Log\Api\V1\Entity\Log;
use Log\Api\V1\Hydrator\LogHydrator;
use Log\Api\V1\Hydrator\LogHalHydrator;

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
            'hydrators' => $this->getHydratorPluginConfig(),
            'doctrine' => $this->getDoctrine(),
            'Mezzio\Hal\Metadata\MetadataMap' => $this->getMetadataMap()
        ];
    }

    public function getDoctrine() : array
    {
        return [
            'driver' => [
                'orm_default' => [
                    'drivers' => [
                        'Log\Api\V1\Entity' => 'log'
                    ]
                ],
                'log' => [
                    'class' =>
                        \Doctrine\ORM\Mapping\Driver\AnnotationDriver::class,
                    'cache' => 'array',
                    'paths' => __DIR__ . '/Entity'
                ]
            ]
        ];
    }

    public function getDependencies() : array
    {
        return [
            'invokables' => [],
            'factories' => [
                Facade\LogFacade::class =>
                    Factory\Facade\LogFacadeFactory::class,
                Action\ListUserLogAction::class =>
                    Factory\Action\ListUserLogFactory::class,
                Action\ListPasswordLogAction::class =>
                    Factory\Action\ListPasswordLogFactory::class,
                Action\GetLogAction::class =>
                    Factory\Action\GetLogFactory::class
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
                'collection_class' => Collection\UserLogCollection::class,
                'collection_relation' => 'logs',
                'route' => 'api.v1.users.logs.list'
            ],
            [
                '__class__' => RouteBasedCollectionMetadata::class,
                'collection_class' => Collection\PasswordLogCollection::class,
                'collection_relation' => 'logs',
                'route' => 'api.v1.passwords.logs.list'
            ],
            [
                '__class__' => RouteBasedResourceMetadata::class,
                'resource_class' => Log::class,
                'route' => 'api.v1.logs.get',
                'extractor' => LogHalHydrator::class,
                'resource_identifier' => 'log_id',
                'route_identifier_placeholder' => 'id'
            ]
        ];
    }

    /**
     * Returns the route configuration
     *
     * @return array
     */
    public function getRoutes() : array
    {
        return [
        /**
         * Users' logs routes
         */
            [
                'name' => 'api.v1.users.logs.list',
                'path' => '/api/v1/users/:id/logs',
                'options' => [
                    'constraints' => ['id' => '\d+'] // controllo che id sia solo numerico
                ],
                'middleware' => [Action\ListUserLogAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.passwords.logs.list',
                'path' => '/api/v1/passwords/:id/logs',
                'options' => [
                    'constraints' => ['id' => '\d+'] // controllo che id sia solo numerico
                ],
                'middleware' => [Action\ListPasswordLogAction::class],
                'allowed_methods' => ['GET']
            ],
            [
                'name' => 'api.v1.logs.get',
                'path' => '/api/v1/logs/:id',
                'options' => [
                    'constraints' => [
                        'id' => '\d+'
                    ] // controllo che id sia solo numerico
                ],
                'middleware' => [Action\GetLogAction::class],
                'allowed_methods' => ['GET']
            ]
        ];
    }

    /**
     *
     * Returns hydrator plugin configuration.
     *
     * @return array
     */
    public function getHydratorPluginConfig() : array
    {
        return [
            'factories' => [
                Hydrator\LogHydrator::class =>
                    Factory\Hydrator\LogHydratorFactory::class,
                Hydrator\LogHalHydrator::class =>
                    Factory\Hydrator\LogHalHydratorFactory::class
            ]
        ];
    }
}
