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
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Action\ListPasswordAction;

class ListPasswordFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $passwordFacade = $container->get(PasswordFacade::class);
        $permissionFacade = $container->get(PermissionFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );

        return new ListPasswordAction(
            $passwordFacade,
            $permissionFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
