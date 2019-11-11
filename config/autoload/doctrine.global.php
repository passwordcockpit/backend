<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

return [
    'doctrine' => [
        'connection' => [
            'orm_default' => [
                'driver_class' => \Doctrine\DBAL\Driver\PDOMySql\Driver::class,
                'params' => [
                    'url' => '',
                    'driverOptions' => array(
                        1002 => 'SET NAMES utf8'
                    )
                ]
            ]
        ],
        'driver' => [
            'orm_default' => [
                'class' =>
                    \Doctrine\Common\Persistence\Mapping\Driver\MappingDriverChain::class
            ]
        ],
        'authentication' => [
            'orm_default' => [
                'object_manager' => 'Doctrine\ORM\EntityManager',
                'identity_class' => 'User\Api\V1\Entity\User',
                'identity_property' => 'username',
                'credential_property' => 'password'
            ]
        ],
        'dbal' => [
            'types' => [
                'enum' => 'string'
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'auto_generate_proxy_classes' => false
            ]
        ],
        'migrations' => [
            'migrations_directory' => '/docs/generated/doctrine/migrations'
        ]
    ]
];
