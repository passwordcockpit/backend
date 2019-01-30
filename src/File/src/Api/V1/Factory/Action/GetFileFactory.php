<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use File\Api\V1\Action\GetFileAction;
use File\Api\V1\Facade\FileFacade;

class GetFileFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $resourceGenerator = new ResourceGeneratorFactory();

        return new GetFileAction(
            $resourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class),
            $container->get(FileFacade::class)
        );
    }
}
