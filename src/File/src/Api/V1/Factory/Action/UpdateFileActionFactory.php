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
    /**
     * Invoke method, create instance of UpdateFileAction class
     *
     * @param ContainerInterface $container
     * @return UpdateFileAction
     */
    public function __invoke(ContainerInterface $container)
    {
        $halResourceGenerator = new ResourceGeneratorFactory();

        $resourceGenerator = new ResourceGeneratorFactory();

        return new UpdateFileAction(
            $container->get(FileFacade::class),
            $container->get(PasswordFacade::class),
            $halResourceGenerator($container),
            $container->get("config")['upload_config'],
            $container->get(Translator::class),
            $container->get(EntityManagerInterface::class),
            new FileCipher(),
            $container->get("config")['block_cipher']['key'],
            $resourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
