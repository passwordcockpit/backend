<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Factory\Action;

use Interop\Container\ContainerInterface;
use File\Api\V1\Action\DownloadFileAction;
use File\Api\V1\Facade\FileFacade;
use Laminas\I18n\Translator\Translator;

class DownloadFileFactory
{
    /**
     * Invoke method, create instance of DownloadFileAction class
     *
     * @param ContainerInterface $container
     * @return DownloadFileAction
     */
    public function __invoke(ContainerInterface $container)
    {
        return new DownloadFileAction(
            $container->get(FileFacade::class),
            $container->get(Translator::class),
            $container->get("config")['upload_config'],
            $container->get("config")['block_cipher']['key']
        );
    }
}
