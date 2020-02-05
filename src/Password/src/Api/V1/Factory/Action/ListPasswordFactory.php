<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Password\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Password\Api\V1\Action\SearchPasswordAction;
use Password\Api\V1\Facade\PasswordFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Action\ListPasswordAction;

class ListPasswordFactory
{
    /**
     * Invoke method, create instance of ListPasswordAction class
     *
     * @param ContainerInterface $container
     * @return ListPasswordAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new ListPasswordAction(
            $container->get(PasswordFacade::class),
            $container->get(PermissionFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
