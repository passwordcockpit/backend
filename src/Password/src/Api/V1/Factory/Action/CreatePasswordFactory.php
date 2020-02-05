<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Action\CreatePasswordAction;

/**
 * Description of CreatePasswordFactory
 */
class CreatePasswordFactory
{
    /**
     * Invoke method, create instance of CreatePasswordAction class
     *
     * @param ContainerInterface $container
     * @return CreatePasswordAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new CreatePasswordAction(
            $container->get(PasswordFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
