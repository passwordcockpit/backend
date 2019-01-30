<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Action;

use Authorization\Api\V1\Action\AuthorizationLogout;
use Authorization\Api\V1\Facade\TokenUserFacade;
use Psr\Container\ContainerInterface;

class AuthorizationLogoutFactory
{
    /**
     * Invoke method, create instance of AuthorizationLogout class
     *
     * @param ContainerInterface $container
     * @return AuthorizationLogout
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AuthorizationLogout($container->get(TokenUserFacade::class));
    }
}
