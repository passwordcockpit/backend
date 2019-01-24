<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Factory\Facade;

use Authentication\Api\V1\Facade\LoginRequestFacade;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;

class LoginRequestFacadeFactory
{
    /**
     * Invoke method
     *
     * @param ContainerInterface $container
     * @return DossierFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManagerInterface::class);

        return new LoginRequestFacade($entityManager);
    }
}
