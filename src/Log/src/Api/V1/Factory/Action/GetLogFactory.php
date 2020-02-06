<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Log\Api\V1\Facade\LogFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use Log\Api\V1\Action\GetLogAction;

/**
 * Description of GetPasswordLogFactory
 */
class GetLogFactory
{
    /**
     * Invoke method, create instance of GetLogAction class
     *
     * @param ContainerInterface $container
     * @return GetLogAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new GetLogAction(
            $container->get(LogFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
