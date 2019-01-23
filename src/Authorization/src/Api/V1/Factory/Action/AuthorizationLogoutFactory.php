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
    public function __invoke(ContainerInterface $container)
    {
        $tokenUserFacade = $container->get(TokenUserFacade::class);
        $authenticationConfig = $container->get('config')['authentication'];

        return new AuthorizationLogout($tokenUserFacade, $authenticationConfig);
    }
}
