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
use Folder\Api\V1\Action\UpdateFolderUserAction;
use Folder\Api\V1\Facade\FolderUserFacade;

/**
 * Description of UpdateFolderUserFactory
 */
class UpdateFolderUserFactory
{
    /**
     * Invoke method, create instance of UpdateFolderUserAction class
     *
     * @param ContainerInterface $container
     * @return UpdateFolderUserAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new UpdateFolderUserAction(
            $container->get(FolderFacade::class),
            $container->get(UserFacade::class),
            $container->get(FolderUserFacade::class),
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
