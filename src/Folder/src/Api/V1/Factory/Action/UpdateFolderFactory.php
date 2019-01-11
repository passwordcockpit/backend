<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderUserFacade;
use Folder\Api\V1\Facade\FolderFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Folder\Api\V1\Action\UpdateFolderAction;

/**
 * Description of UpdateFolderFactory
 */
class UpdateFolderFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderUserFacade = $container->get(FolderUserFacade::class);
        $folderFacade = $container->get(FolderFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new UpdateFolderAction(
            $folderUserFacade,
            $folderFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
