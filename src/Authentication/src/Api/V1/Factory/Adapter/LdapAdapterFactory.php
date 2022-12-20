<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Factory\Adapter;

use Authentication\Api\V1\Adapter\LdapAdapter;
use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\UserFacade;
use Doctrine\ORM\EntityManagerInterface;

class LdapAdapterFactory
{
    /**
     * Invoke method, create instance of LdapAdapter class
     *
     * @param ContainerInterface $container
     * @return LdapAdapter
     */
    public function __invoke(ContainerInterface $container)
    {
        return new LdapAdapter(
            $container->get(UserFacade::class),
            $container->get('config')['ldap']['servers'],
            $container->get('config')['ldap']['userAttributes'],
            $container->get(EntityManagerInterface::class)
        );
    }
}
