<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Assertion;

use Interop\Container\ContainerInterface;
use Authorization\Api\V1\Assertion\LogsAssertion;
use Folder\Api\V1\Facade\FolderUserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Password\Api\V1\Facade\PasswordFacade;
use Log\Api\V1\Facade\LogFacade;
use Doctrine\ORM\EntityManagerInterface;

class LogsAssertionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $translator = $container->get("translator");
        $folderUserFacade = $container->get(FolderUserFacade::class);
        $permissionFacade = $container->get(PermissionFacade::class);
        $passwordFacade = $container->get(PasswordFacade::class);
        $logFacade = $container->get(LogFacade::class);
        $entityManager = $container->get(EntityManagerInterface::class);

        return new LogsAssertion(
            $translator,
            $folderUserFacade,
            $permissionFacade,
            $passwordFacade,
            $logFacade,
            $entityManager
        );
    }
}
