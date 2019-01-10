<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Action;

use Password\Api\V1\Action\ListPasswordFilesAction;
use Psr\Container\ContainerInterface;
use File\Api\V1\Facade\FileFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Facade\PasswordFacade;

/**
 * Description of ListPasswordFilesAction
 */
class ListPasswordFilesFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $fileFacade = $container->get(FileFacade::class);
        $passwordFacade = $container->get(PasswordFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new ListPasswordFilesAction(
            $fileFacade,
            $passwordFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
