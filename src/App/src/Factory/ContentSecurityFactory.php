<?php

namespace App\Factory;

use Psr\Container\ContainerInterface;
use App\Middleware\ContentSecurityMiddleware;

class ContentSecurityFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');

        // Default value set to "none".
        // Directive "none" implies that every resources are blocked
        $directives = $config['security']['content-security-policy'] ?? 'none';
        return new ContentSecurityMiddleware($directives);
    }
}
