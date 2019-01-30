<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Folder\Api\V1\Action\DeleteFolderAction;
use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of DeleteFolderAction
 */
class DeleteFolderFactory
{
    /**
     * Invoke method, create instance of DeleteFolderAction class
     *
     * @param ContainerInterface $container
     * @return DeleteFolderAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new DeleteFolderAction(
            $container->get(FolderFacade::class),
            $halResourceGenerator($container)
        );
    }
}
