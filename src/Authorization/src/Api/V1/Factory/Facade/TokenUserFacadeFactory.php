<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Facade;

use Authorization\Api\V1\Facade\TokenUserFacade;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class TokenUserFacadeFactory
{
    /**
     * Invoke method, create instance of TokenUserFacade class
     *
     * @param ContainerInterface $container
     * @return TokenUserFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        return new TokenUserFacade(
            $container->get(EntityManagerInterface::class)
        );
    }
}
