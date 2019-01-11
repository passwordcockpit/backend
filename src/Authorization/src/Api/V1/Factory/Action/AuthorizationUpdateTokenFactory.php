<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use Authorization\Api\V1\Action\AuthorizationUpdateToken;
use Doctrine\ORM\EntityManagerInterface;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

class AuthorizationUpdateTokenFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $problemDetailsFactory = $container->get(
            ProblemDetailsResponseFactory::class
        );
        $authenticationConfig = $container->get('config')['authentication'];
        return new AuthorizationUpdateToken(
            $problemDetailsFactory,
            $authenticationConfig
        );
    }
}
