<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Factory\Facade;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Laminas\I18n\Translator\Translator;
use Authentication\Api\V1\Facade\LoginRequestFacade;

class LoginRequestFacadeFactory
{
    /**
     * Invoke method
     *
     * @param ContainerInterface $container
     * @return LoginRequestFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        return new LoginRequestFacade(
            $container->get(Translator::class),
            $container->get(EntityManagerInterface::class)
        );
    }
}
