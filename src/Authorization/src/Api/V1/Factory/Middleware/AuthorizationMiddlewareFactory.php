<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use Zend\Permissions\Rbac\Rbac;
use Authorization\Api\V1\Middleware\AuthorizationMiddleware;
use Exception;
use Folder\Api\V1\Facade\FolderUserFacade;
use User\Api\V1\Facade\UserFacade;
use Password\Api\V1\Facade\PasswordFacade;
use User\Api\V1\Facade\PermissionFacade;
use Doctrine\ORM\EntityManagerInterface;
use Authorization\Api\V1\AssertionPluginManager;
use Zend\I18n\Translator\Translator;

class AuthorizationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');

        if (!isset($config['rbac']['roles'])) {
            throw new Exception('Rbac roles are not configured');
        }
        if (!isset($config['rbac']['permissions'])) {
            throw new Exception('Rbac permissions are not configured');
        }

        $rbac = new Rbac();
        $rbac->setCreateMissingRoles(true);

        // roles and parents
        foreach ($config['rbac']['roles'] as $role => $parents) {
            $rbac->addRole($role, $parents);
        }

        // permissions
        foreach ($config['rbac']['permissions'] as $role => $permissions) {
            foreach ($permissions as $perm) {
                $rbac->getRole($role)->addPermission($perm);
            }
        }

        return new AuthorizationMiddleware(
            $rbac,
            $container->get(FolderUserFacade::class),
            $container->get(Translator::class),
            $container->get(EntityManagerInterface::class),
            $container->get(UserFacade::class),
            $container->get(PasswordFacade::class),
            $container->get(PermissionFacade::class),
            $container->get(AssertionPluginManager::class)
        );
    }
}
