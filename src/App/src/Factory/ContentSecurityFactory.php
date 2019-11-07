<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Factory;

use Psr\Container\ContainerInterface;
use App\Middleware\ContentSecurityMiddleware;

class ContentSecurityFactory
{
    /**
     * Invoke method, create instance of ContentSecurityMiddleware class
     *
     * @param ContainerInterface $container
     * @return ContentSecurityMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');

        // Default value set to "none".
        // Directive "none" implies that every resources are blocked
        $directives = $config['security']['content-security-policy'] ?? 'none';
        return new ContentSecurityMiddleware($directives);
    }
}
