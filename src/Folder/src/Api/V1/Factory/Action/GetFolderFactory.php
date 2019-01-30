<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Folder\Api\V1\Action\GetFolderAction;
use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderUserFacade;
use Folder\Api\V1\Facade\FolderFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Zend\Expressive\Hal\HalResponseFactory;

/**
 * Description of GetFolderFactory
 */
class GetFolderFactory
{
    /**
     * Invoke method, create instance of GetFolderAction class
     *
     * @param ContainerInterface $container
     * @return GetFolderAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new GetFolderAction(
            $container->get(FolderUserFacade::class),
            $container->get(FolderFacade::class),
            $halResourceGenerator($container),
            $container->get(HalResponseFactory::class)
        );
    }
}
