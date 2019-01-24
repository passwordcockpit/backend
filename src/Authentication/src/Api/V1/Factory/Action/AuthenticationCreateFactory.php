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
use Authorization\Api\V1\Facade\TokenUserFacade;
use Zend\I18n\Translator\Translator;
use Authentication\Api\V1\Facade\LoginRequestFacade;

class AuthenticationCreateFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $authenticationConfig = $container->get('config')['authentication'];
        $translator = $container->get(Translator::class);
        $authenticationAdapter = $container->get(
            \Zend\Authentication\Adapter\AdapterInterface::class
        );
        $tokenUserFacade = $container->get(TokenUserFacade::class);
        $loginRequestFacade = $container->get(LoginRequestFacade::class);

        return new AuthenticationCreateAction(
            $authenticationConfig,
            $translator,
            $authenticationAdapter,
            $tokenUserFacade,
            $loginRequestFacade
        );
    }
}
