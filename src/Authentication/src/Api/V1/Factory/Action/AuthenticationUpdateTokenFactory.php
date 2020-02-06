<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use Authentication\Api\V1\Action\AuthenticationUpdateToken;
use Doctrine\ORM\EntityManagerInterface;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Authentication\Api\V1\Facade\TokenUserFacade;

class AuthenticationUpdateTokenFactory
{
    /**
     * Invoke method, create instance of AuthenticationUpdateToken class
     *
     * @param ContainerInterface $container
     * @return AuthenticationUpdateToken
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationUpdateToken(
            $container->get(ProblemDetailsResponseFactory::class),
            $container->get('config')['authentication'],
            $container->get(TokenUserFacade::class)
        );
    }
}
