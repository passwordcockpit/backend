<?php
use Laminas\Authentication\Adapter\AdapterInterface;

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */
return [
    'authentication' => [
        'secure' => false,
        'expiration_time' => 15, // minutes
        'hard_timeout' => 240, //minutes
        'max_requests_per_timespan' => 6, //maximum failed login requests in attempt_timespan
        'attempt_timespan' => 15 // time to check how many failed attempts were made minutes
    ],
    'dependencies' => [
        'factories' => [
            Laminas\Authentication\Adapter\AdapterInterface::class =>
                Authentication\Api\V1\Factory\Adapter\DoctrineAdapterFactory::class //for LDAP Authentication\Api\V1\Factory\Adapter\LdapAdapterFactory::class
        ]
    ]
];
