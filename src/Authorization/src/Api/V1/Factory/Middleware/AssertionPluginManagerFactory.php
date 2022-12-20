<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Factory\Middleware;

use Interop\Container\ContainerInterface;
use Authorization\Api\V1\AssertionPluginManager;
use Doctrine\ORM\EntityManagerInterface;
use Folder\Api\V1\Facade\FolderUserFacade;
use Laminas\I18n\Translator\Translator;

class AssertionPluginManagerFactory
{
    /**
     * Invoke method, create instance of AssertionPluginManager class
     *
     * @param ContainerInterface $container
     * @return AssertionPluginManager
     */
    public function __invoke(ContainerInterface $container)
    {
        $config = $container->get('config');

        $assertionConfig = $config['rbac']['assertions'] ?? null;
        $assertionFactories = $assertionConfig['factories'] ?? [];
        $assertionRegisteredByRoute =
            $assertionConfig['registeredByRoute'] ?? [];

        return new AssertionPluginManager(
            $container->get(Translator::class),
            $container->get(FolderUserFacade::class),
            $container->get(EntityManagerInterface::class),
            $container,
            ['factories' => $assertionFactories],
            $assertionRegisteredByRoute,
        );
    }
}
