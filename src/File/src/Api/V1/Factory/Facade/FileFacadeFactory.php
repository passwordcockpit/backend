<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Factory\Facade;

use Interop\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use File\Api\V1\Facade\FileFacade;
use File\Api\V1\Entity\File;
use File\Api\V1\Hydrator\FileHydrator;
use Zend\I18n\Translator\Translator;

class FileFacadeFactory
{
    /**
     * Invoke method
     *
     * @param ContainerInterface $container
     * @return FileFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        return new FileFacade(
            $container->get(Translator::class),
            $container->get(EntityManagerInterface::class),
            File::class,
            $container->get(FileHydrator::class),
            $container->get("config")['upload_config']
        );
    }
}
