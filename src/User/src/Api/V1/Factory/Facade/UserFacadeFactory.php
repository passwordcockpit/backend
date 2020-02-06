<?php

/**
 * Description of UserFacadeFactory
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Facade;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\UserFacade;
use Laminas\I18n\Translator\Translator;

class UserFacadeFactory
{
    /**
     * Invoke method, create instance of UserFacade class
     *
     * @param ContainerInterface $container
     * @return UserFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        return new UserFacade(
            $container->get(EntityManagerInterface::class),
            $container->get(Translator::class)
        );
    }
}
