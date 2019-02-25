<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use Authentication\Api\V1\Middleware\AuthenticationMiddleware;
use User\Api\V1\Facade\UserFacade;
use Authentication\Api\V1\Facade\TokenUserFacade;
use Zend\I18n\Translator\Translator;

class AuthenticationMiddlewareFactory
{
    /**
     *
     * Invoke method, create instance of AuthenticationMiddleware class
     *
     * @param ContainerInterface $container
     * @return AuthenticationMiddleware
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationMiddleware(
            $container->get(Translator::class),
            $container->get(UserFacade::class),
            $container->get(TokenUserFacade::class),
            $container->get(
                \Zend\Authentication\Adapter\AdapterInterface::class
            )
        );
    }
}
