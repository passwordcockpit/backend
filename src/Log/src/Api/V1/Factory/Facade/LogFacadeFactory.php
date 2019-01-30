<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Facade;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Log\Api\V1\Facade\LogFacade;
use Zend\I18n\Translator\Translator;

/**
 * Description of LogFacadeFactory
 */
class LogFacadeFactory
{
    /**
     * Invoke method, create instance of LogFacade class
     *
     * @param ContainerInterface $container
     * @return LogFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        return new LogFacade(
            $container->get(EntityManagerInterface::class),
            $container->get(Translator::class)
        );
    }
}
