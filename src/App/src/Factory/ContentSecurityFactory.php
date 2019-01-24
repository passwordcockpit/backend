<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use App\Middleware\ContentSecurityMiddleware;

class ContentSecurityFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ContentSecurityMiddleware();
    }
}
