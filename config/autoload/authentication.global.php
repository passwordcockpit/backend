<?php
use Zend\Authentication\Adapter\AdapterInterface;

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */
return [
    'authentication' => [
        'secure' => true,
        'expiration_time' => 600, // minutes
        'type' => 'db' // ldap or db
    ],
    'dependencies' => [
        'factories' => [
            Zend\Authentication\Adapter\AdapterInterface::class =>
                Authentication\Api\V1\Factory\Adapter\DoctrineAdapterFactory::class
        ]
    ]
];
