<?php

namespace Authentication\Api\V1\Factory\Adapter;

use Authentication\Api\V1\Adapter\LdapAdapter;
use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\UserFacade;

class LdapAdapterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LdapAdapter(
            $container->get(UserFacade::class),
            $container->get('config')['ldap']
        );
    }
}
