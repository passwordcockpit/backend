<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use Authentication\Api\V1\Middleware\AuthenticationMiddleware;
use User\Api\V1\Facade\UserFacade;

class AuthenticationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $userFacade = $container->get(UserFacade::class);
        $translator = $container->get("translator");

        return new AuthenticationMiddleware($translator, $userFacade);
    }
}
