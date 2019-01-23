<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use App\Middleware\StrictTransportSecurityMiddleware;

class StrictTransportSecurityFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new StrictTransportSecurityMiddleware();
    }
}
