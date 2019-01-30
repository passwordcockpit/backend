<?php

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
