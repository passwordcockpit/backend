<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderUserFacade;
use Folder\Api\V1\Facade\FolderFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use Folder\Api\V1\Action\UpdateFolderAction;

/**
 * Description of UpdateFolderFactory
 */
class UpdateFolderFactory
{
    /**
     * Invoke method, create instance of UpdateFolderAction class
     *
     * @param ContainerInterface $container
     * @return UpdateFolderAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new UpdateFolderAction(
            $container->get(FolderUserFacade::class),
            $container->get(FolderFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
