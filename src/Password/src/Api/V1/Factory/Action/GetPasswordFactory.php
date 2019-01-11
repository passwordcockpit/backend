<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Action;

use Password\Api\V1\Action\GetPasswordAction;
use Psr\Container\ContainerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use File\Api\V1\Facade\FileFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of GetPasswordFactory
 */
class GetPasswordFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderFacade = $container->get(PasswordFacade::class);
        $fileFacade = $container->get(FileFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new GetPasswordAction(
            $folderFacade,
            $fileFacade,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
