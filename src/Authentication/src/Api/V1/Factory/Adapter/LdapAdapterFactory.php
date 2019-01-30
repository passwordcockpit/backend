<?php

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
            $container->get('config')['ldap'],
            $container->get(EntityManagerInterface::class)
        );
    }
}
