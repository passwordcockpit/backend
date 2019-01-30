<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Action;

use User\Api\V1\Action\CreateUserAction;
use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of CreateUserFactory
 */
class CreateUserFactory
{
    /**
     * Invoke method, create instance of CreateUserAction class
     *
     * @param ContainerInterface $container
     * @return CreateUserAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new CreateUserAction(
            $container->get(UserFacade::class),
            $container->get(PermissionFacade::class),
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
