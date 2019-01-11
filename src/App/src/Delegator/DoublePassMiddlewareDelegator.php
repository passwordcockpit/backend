<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Delegator;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use function Zend\Stratigility\doublePassMiddleware;

/*
 * Delegator to map to any services that continue to use the double-pass signature and it didn't support the new PSR-15 signature
 * To use it you need mapping this class into the service manager config key "delegators"
 * E.g: 'delegators' => [
 *           DoublePassDontSupportedClass::class => [
 *               DoublePassMiddlewareDelegator::class,
 *           ],
 *       ],
 *  ]
 */

class DoublePassMiddlewareDelegator
{
    public function __invoke(
        ContainerInterface $container,
        string $serviceName,
        callable $callback
    ) {
        return doublePassMiddleware(
            $callback(),
            $container->get(ResponseInterface::class)()
        );
    }
}
