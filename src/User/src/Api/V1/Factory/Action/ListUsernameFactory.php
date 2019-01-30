<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use User\Api\V1\Action\ListUsernameAction;
use User\Api\V1\Facade\UserFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

class ListUsernameFactory
{
    /**
     * Invoke method, create instance of ListUsernameAction class
     *
     * @param ContainerInterface $container
     * @return ListUsernameAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new ListUsernameAction(
            $container->get(UserFacade::class),
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
