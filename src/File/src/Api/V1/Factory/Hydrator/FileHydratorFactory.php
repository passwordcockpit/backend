<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 *
 * Hydrator Factory used only when generating the HAL representation for the Group entity.
 */

namespace File\Api\V1\Factory\Hydrator;

use File\Api\V1\Hydrator\FileHydrator;
use Psr\Container\ContainerInterface;

class FileHydratorFactory
{
    /**
     *
     * @param ContainerInterface $container
     * @return FileHydrator
     */
    public function __invoke(ContainerInterface $container)
    {
        return new FileHydrator();
    }
}
