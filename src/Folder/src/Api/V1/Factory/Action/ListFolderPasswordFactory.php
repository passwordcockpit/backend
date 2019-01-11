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
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Facade\PasswordFacade;

/**
 * Description of ListFolderPasswordFactory
 */
class ListFolderPasswordFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderFacade = $container->get(FolderFacade::class);
        $passwordFacade = $container->get(PasswordFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new ListFolderPasswordAction(
            $folderFacade,
            $passwordFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
