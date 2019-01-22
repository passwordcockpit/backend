<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Factory\Action;

use File\Api\V1\Facade\FileFacade;
use Doctrine\ORM\EntityManagerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Psr\Container\ContainerInterface;
use File\Api\V1\Action\UpdateFileAction;
use Zend\Crypt\FileCipher;
use Zend\I18n\Translator\Translator;

class UpdateFileActionFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $fileFacade = $container->get(FileFacade::class);
        $passwordFacade = $container->get(PasswordFacade::class);
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $uploadConfig = $container->get("config")['upload_config'];
        $translator = $container->get(Translator::class);
        $entityManager = $container->get(EntityManagerInterface::class);
        $fileCipher = new FileCipher();
        $encriptionKey = $container->get("config")['block_cipher']['key'];

        $resourceGenerator = new ResourceGeneratorFactory();
        $resourceGeneratorInstance = $resourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );

        return new UpdateFileAction(
            $fileFacade,
            $passwordFacade,
            $halResourceGeneratorInstance,
            $uploadConfig,
            $translator,
            $entityManager,
            $fileCipher,
            $encriptionKey,
            $resourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
