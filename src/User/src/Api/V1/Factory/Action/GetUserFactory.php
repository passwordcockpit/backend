<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Action;

/**
 * Description of GetUserFactory
 */
use Psr\Container\ContainerInterface;
use User\Api\V1\Action\GetUserAction;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGeneratorFactory;

class GetUserFactory
{
    /**
     * Invoke method, create instance of GetUserAction class
     *
     * @param ContainerInterface $container
     * @return GetUserAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new GetUserAction(
            $container->get(UserFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
