<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use User\Api\V1\Action\UpdateUserPermissionAction;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\Hal\ResourceGeneratorFactory;

/**
 * Description of UpdateUserPermissionFactory
 */
class UpdateUserPermissionFactory
{
    /**
     * Invoke method, create instance of UpdateUserPermissionAction class
     *
     * @param ContainerInterface $container
     * @return UpdateUserPermissionAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new UpdateUserPermissionAction(
            $container->get(UserFacade::class),
            $container->get(PermissionFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
