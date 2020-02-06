<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use Mezzio\Hal\ResourceGeneratorFactory;
use File\Api\V1\Action\GetFileAction;
use File\Api\V1\Facade\FileFacade;

class GetFileFactory
{
    /**
     * Invoke method, create instance of GetFileAction class
     *
     * @param ContainerInterface $container
     * @return GetFileAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $resourceGenerator = new ResourceGeneratorFactory();

        return new GetFileAction(
            $resourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class),
            $container->get(FileFacade::class)
        );
    }
}
