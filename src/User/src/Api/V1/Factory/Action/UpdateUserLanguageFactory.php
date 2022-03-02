<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Christian Willemse <christian.willemse@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use User\Api\V1\Action\UpdateUserAction;
use Authentication\Api\V1\Facade\TokenUserFacade;
use User\Api\V1\Action\UpdateUserLanguageAction;

/**
 * Description of UpdateUserLanguageFactory
 */
class UpdateUserLanguageFactory
{
    /**
     * Invoke method, create instance of UpdateUserAction class
     *
     * @param ContainerInterface $container
     * @return UpdateUserAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new UpdateUserLanguageAction(
            $container->get(UserFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class),
            $container->get('config')['authentication'],
            $container->get(TokenUserFacade::class)
        );
    }
}
