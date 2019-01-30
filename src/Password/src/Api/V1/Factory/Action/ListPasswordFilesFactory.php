<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
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
    /**
     * Invoke method, create instance of ListPasswordFilesAction class
     *
     * @param ContainerInterface $container
     * @return ListPasswordFilesAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new ListPasswordFilesAction(
            $container->get(FileFacade::class),
            $container->get(PasswordFacade::class),
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
