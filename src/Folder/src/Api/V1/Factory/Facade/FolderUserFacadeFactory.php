<?php

/**
 * Description of UserFacadeFactory
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Facade;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use User\Api\V1\Facade\UserFacade;
use Folder\Api\V1\Facade\FolderUserFacade;

class FolderUserFacadeFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $translator = $container->get("translator");
        $userFacade = $container->get(UserFacade::class);
        $folderFacade = $container->get(FolderFacade::class);
        return new FolderUserFacade(
            $entityManager,
            $translator,
            $userFacade,
            $folderFacade
        );
    }
}
