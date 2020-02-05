<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Action;

use Folder\Api\V1\Facade\FolderUserFacade;
use Psr\Container\ContainerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Mezzio\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Action\MovePasswordAction;
use User\Api\V1\Facade\PermissionFacade;
use User\Api\V1\Facade\UserFacade;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

/**
 * Description of MovePasswordFactory
 */
class MovePasswordFactory
{
    /**
     * Invoke method, create instance of MovePasswordAction class
     *
     * @param ContainerInterface $container
     * @return MovePasswordAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new MovePasswordAction(
            $container->get(PasswordFacade::class),
            $container->get(ProblemDetailsResponseFactory::class),
            $container->get(FolderUserFacade::class),
            $container->get(UserFacade::class),
            $container->get(PermissionFacade::class)
        );
    }
}
