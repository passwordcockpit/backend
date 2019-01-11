<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Hydrator;

use Psr\Container\ContainerInterface;
use Log\Api\V1\Hydrator\LogHydrator;

/**
 * Description of GetPasswordLogFactory
 */
class LogHydratorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new LogHydrator($container->get("translator"));
    }
}
