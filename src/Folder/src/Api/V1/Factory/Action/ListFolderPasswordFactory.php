<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Factory\Action;

use Folder\Api\V1\Action\ListFolderPasswordAction;
use Psr\Container\ContainerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Facade\PasswordFacade;

/**
 * Description of ListFolderPasswordFactory
 */
class ListFolderPasswordFactory
{
    /**
     * Invoke method, create instance of ListFolderPasswordAction class
     *
     * @param ContainerInterface $container
     * @return ListFolderPasswordAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new ListFolderPasswordAction(
            $container->get(FolderFacade::class),
            $container->get(PasswordFacade::class),
            $halResourceGenerator($container),
            $container->get(\Mezzio\Hal\HalResponseFactory::class)
        );
    }
}
