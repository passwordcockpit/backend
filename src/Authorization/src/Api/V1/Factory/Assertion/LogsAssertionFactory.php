<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
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
use Zend\I18n\Translator\Translator;

class LogsAssertionFactory
{
    /**
     * Invoke method, create instance of LogsAssertion class
     *
     * @param ContainerInterface $container
     * @return LogsAssertion
     */
    public function __invoke(ContainerInterface $container)
    {
        return new LogsAssertion(
            $container->get(Translator::class),
            $container->get(FolderUserFacade::class),
            $container->get(PermissionFacade::class),
            $container->get(PasswordFacade::class),
            $container->get(LogFacade::class),
            $container->get(EntityManagerInterface::class)
        );
    }
}
