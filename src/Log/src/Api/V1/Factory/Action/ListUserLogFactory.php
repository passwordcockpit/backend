<?php

/**
 * @see https://github.com/passwordcockpitbackend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpitbackend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Action;

use Log\Api\V1\Action\ListUserLogAction;
use Psr\Container\ContainerInterface;
use Log\Api\V1\Facade\LogFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of ListUserLogFactory
 */
class ListUserLogFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $logFacade = $container->get(LogFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        $paginatorConfig = $container->get("config")['paginator_config'];
        return new ListUserLogAction(
            $logFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory,
            $paginatorConfig
        );
    }
}
