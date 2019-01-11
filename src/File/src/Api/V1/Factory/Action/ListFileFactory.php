<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use File\Api\V1\Action\ListFileAction;

/**
 * Description of ListFilesFactory
 *
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */
class ListFileFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new ListFileAction();
    }
}
