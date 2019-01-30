<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Folder\Api\V1\Action\CreateFolderAction;
use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of CreateFolderFactory
 */
class CreateFolderFactory
{
    /**
     * Invoke method, create instance of CreateFolderAction class
     *
     * @param ContainerInterface $container
     * @return CreateFolderAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new CreateFolderAction(
            $container->get(FolderFacade::class),
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
