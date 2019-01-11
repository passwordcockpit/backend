<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Action;

use Password\Api\V1\Action\DeletePasswordAction;
use Psr\Container\ContainerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;

/**
 * Description of DeletePasswordFactory
 */
class DeletePasswordFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $folderFacade = $container->get(PasswordFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        return new DeletePasswordAction(
            $folderFacade,
            $halResourceGeneratorInstance
        );
    }
}
