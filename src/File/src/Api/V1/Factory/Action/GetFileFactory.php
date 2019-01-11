<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
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
        $resourceGeneratorInstance = $resourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new GetFileAction(
            $resourceGeneratorInstance,
            $halResponseFactory,
            $container->get(FileFacade::class)
        );
    }
}
