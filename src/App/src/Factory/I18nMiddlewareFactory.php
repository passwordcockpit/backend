<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Factory;

use Psr\Container\ContainerInterface;

class I18nMiddlewareFactory
{
    /**
     * @param ContainerInterface $container
     */
    public function __invoke(ContainerInterface $container)
    {
        return new \App\Middleware\I18nMiddleware(
            $container->get("translator"),
            $container->get("config")['locale']
        );
    }
}
