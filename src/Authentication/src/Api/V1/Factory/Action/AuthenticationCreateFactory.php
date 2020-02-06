<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use Authentication\Api\V1\Action\AuthenticationCreateAction;
use Doctrine\ORM\EntityManagerInterface;
use User\Api\V1\Facade\PermissionFacade;
use Authentication\Api\V1\Facade\TokenUserFacade;
use Laminas\I18n\Translator\Translator;
use Authentication\Api\V1\Facade\LoginRequestFacade;

class AuthenticationCreateFactory
{
    /**
     * Invoke method, create instance of AuthenticationCreateAction class
     *
     * @param ContainerInterface $container
     * @return AuthenticationCreateAction
     */
    public function __invoke(ContainerInterface $container)
    {
        return new AuthenticationCreateAction(
            $container->get('config')['authentication'],
            $container->get(Translator::class),
            $container->get(
                \Laminas\Authentication\Adapter\AdapterInterface::class
            ),
            $container->get(TokenUserFacade::class),
            $container->get(LoginRequestFacade::class)
        );
    }
}
