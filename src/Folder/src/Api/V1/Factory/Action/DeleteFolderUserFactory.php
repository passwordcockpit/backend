<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use User\Api\V1\Facade\UserFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Folder\Api\V1\Action\DeleteFolderUserAction;
use Folder\Api\V1\Facade\FolderUserFacade;

/**
 * Description of DeleteFolderUserFactory
 */
class DeleteFolderUserFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderFacade = $container->get(FolderFacade::class);
        $userFacade = $container->get(UserFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $folderUserFacade = $container->get(FolderUserFacade::class);
        return new DeleteFolderUserAction(
            $folderFacade,
            $halResourceGeneratorInstance,
            $userFacade,
            $folderUserFacade
        );
    }
}
