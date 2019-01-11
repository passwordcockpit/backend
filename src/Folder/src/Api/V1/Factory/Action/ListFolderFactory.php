<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Folder\Api\V1\Action\ListFolderAction;
use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of ListFolderFactory
 */
class ListFolderFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderFacade = $container->get(FolderFacade::class);
        $userFacade = $container->get(UserFacade::class);
        $permissionFacade = $container->get(PermissionFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new ListFolderAction(
            $folderFacade,
            $userFacade,
            $permissionFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
