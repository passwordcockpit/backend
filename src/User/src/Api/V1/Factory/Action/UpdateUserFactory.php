<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\UserFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use User\Api\V1\Action\UpdateUserAction;
use Authorization\Api\V1\Facade\TokenUserFacade;

/**
 * Description of UpdateUserFactory
 */
class UpdateUserFactory
{
    //put your code here
    public function __invoke(ContainerInterface $container)
    {
        $userFacade = $container->get(UserFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        $authenticationConfig = $container->get('config')['authentication'];
        $tokenUserFacade = $container->get(TokenUserFacade::class);

        return new UpdateUserAction(
            $userFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory,
            $authenticationConfig,
            $tokenUserFacade
        );
    }
}
