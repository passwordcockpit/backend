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
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Log\Api\V1\Action\GetLogAction;

/**
 * Description of GetPasswordLogFactory
 */
class GetLogFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $logFacade = $container->get(LogFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new GetLogAction(
            $logFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
