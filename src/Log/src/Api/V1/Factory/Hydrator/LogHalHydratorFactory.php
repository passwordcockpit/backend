<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Hydrator;

use Psr\Container\ContainerInterface;
use Log\Api\V1\Hydrator\LogHalHydrator;

/**
 * Description of GetPasswordLogFactory
 */
class LogHalHydratorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LogHalHydrator($container->get("translator"));
    }
}
