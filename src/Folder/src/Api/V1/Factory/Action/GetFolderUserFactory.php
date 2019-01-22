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
use User\Api\V1\Facade\UserFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Folder\Api\V1\Action\GetFolderUserAction;
use Zend\I18n\Translator\Translator;

/**
 * Description of GetFolderUserFactory
 */
class GetFolderUserFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderUserFacade = $container->get(FolderUserFacade::class);
        $folderFacade = $container->get(FolderFacade::class);
        $userFacade = $container->get(UserFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        $translator = $container->get(Translator::class);

        return new GetFolderUserAction(
            $folderUserFacade,
            $folderFacade,
            $userFacade,
            $translator,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
