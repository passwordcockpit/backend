<?php

/**
 * @see https://github.com/passwordcockpitbackend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpitbackend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Factory\Facade;

use Interop\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;
use File\Api\V1\Facade\FileFacade;
use File\Api\V1\Entity\File;
use File\Api\V1\Hydrator\FileHydrator;

class FileFacadeFactory
{
    /**
     * Invoke method
     *
     * @param ContainerInterface $container
     * @return DossierFacade
     */
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $translator = $container->get("translator");
        $fileHydrator = $container->get(FileHydrator::class);
        $uploadConfig = $container->get("config")['upload_config'];
        //$passwordFacade = $container->get(PasswordFacade::class);

        return new FileFacade(
            $translator,
            $entityManager,
            File::class,
            $fileHydrator,
            $uploadConfig
        );
    }
}
