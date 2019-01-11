<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Facade;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Crypt\BlockCipher;
use Zend\Crypt\FileCipher;
use Folder\Api\V1\Facade\FolderFacade;
use Log\Api\V1\Facade\LogFacade;
use File\Api\V1\Facade\FileFacade;

/**
 * Description of PasswordFacadeFactory
 */

class PasswordFacadeFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $entityManager = $container->get(EntityManagerInterface::class);
        $translator = $container->get("translator");
        $blockCipher = BlockCipher::factory(
            $container->get("config")['block_cipher']['encryption_library'],
            $container->get("config")['block_cipher']['algorithms']
        );
        $fileCipher = new FileCipher();
        $encriptionKey = $container->get("config")['block_cipher']['key'];
        $folderFacade = $container->get(FolderFacade::class);
        $logFacade = $container->get(LogFacade::class);
        $fileFacade = $container->get(FileFacade::class);
        $uploadConfig = $container->get("config")['upload_config'];
        return new PasswordFacade(
            $entityManager,
            $translator,
            $blockCipher,
            $fileCipher,
            $encriptionKey,
            $folderFacade,
            $logFacade,
            $fileFacade,
            $uploadConfig
        );
    }
}
