<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Factory;

use Psr\Container\ContainerInterface;
use App\Middleware\StrictTransportSecurityMiddleware;

class StrictTransportSecurityFactory
{
    /**
     * Invoke method, create instance of StrictTransportSecurityMiddleware class
     *
     * @param ContainerInterface $container
     * @return StrictTransportSecurityMiddleware
     */

    public function __invoke(ContainerInterface $container)
    {
        return new StrictTransportSecurityMiddleware($container->get('config'));
    }
}
