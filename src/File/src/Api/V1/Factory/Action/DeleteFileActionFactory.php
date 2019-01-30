<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Factory\Action;

use File\Api\V1\Facade\FileFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Psr\Container\ContainerInterface;
use File\Api\V1\Action\DeleteFileAction;

class DeleteFileActionFactory
{
    /**
     * Invoke method, create instance of DeleteFileAction class
     *
     * @param ContainerInterface $container
     * @return DeleteFileAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();
        return new DeleteFileAction(
            $container->get(FileFacade::class),
            $halResourceGenerator($container)
        );
    }
}
