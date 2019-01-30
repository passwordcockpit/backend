<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use File\Api\V1\Action\DownloadFileAction;
use File\Api\V1\Facade\FileFacade;
use Zend\Crypt\FileCipher;
use Zend\I18n\Translator\Translator;

class DownloadFileFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $resourceGenerator = new ResourceGeneratorFactory();

        return new DownloadFileAction(
            $resourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class),
            $container->get(FileFacade::class),
            $container->get(Translator::class),
            $container->get("config")['upload_config'],
            new FileCipher(),
            $container->get("config")['block_cipher']['key']
        );
    }
}
