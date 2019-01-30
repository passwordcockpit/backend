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
use Zend\I18n\Translator\Translator;

/**
 * Description of PasswordFacadeFactory
 */

class PasswordFacadeFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new PasswordFacade(
            $container->get(EntityManagerInterface::class),
            $container->get(Translator::class),
            BlockCipher::factory(
                $container->get("config")['block_cipher']['encryption_library'],
                $container->get("config")['block_cipher']['algorithms']
            ),
            new FileCipher(),
            $container->get("config")['block_cipher']['key'],
            $container->get(FolderFacade::class),
            $container->get(LogFacade::class),
            $container->get(FileFacade::class),
            $container->get("config")['upload_config']
        );
    }
}
