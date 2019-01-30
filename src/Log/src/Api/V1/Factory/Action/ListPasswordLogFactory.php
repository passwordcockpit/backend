<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Action;

use Log\Api\V1\Action\ListPasswordLogAction;
use Psr\Container\ContainerInterface;
use Log\Api\V1\Facade\LogFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of ListPasswordLogFactory
 */
class ListPasswordLogFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new ListPasswordLogAction(
            $container->get(LogFacade::class),
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class),
            $container->get("config")['paginator_config']
        );
    }
}
