<?php

/**
 * Description of PermissionFacadeFactory
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Facade;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use User\Api\V1\Facade\PermissionFacade;
use User\Api\V1\Facade\UserFacade;
use Zend\I18n\Translator\Translator;

class PermissionFacadeFactory
{
    //put your code here
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $translator = $container->get(Translator::class);
        $userFacade = $container->get(UserFacade::class);
        return new PermissionFacade($entityManager, $translator, $userFacade);
    }
}
