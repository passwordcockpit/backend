<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Factory\Hydrator;

use Psr\Container\ContainerInterface;
use Log\Api\V1\Hydrator\LogHydrator;
use Zend\I18n\Translator\Translator;

/**
 * Description of GetPasswordLogFactory
 */
class LogHydratorFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $translator = $container->get(Translator::class);

        return new LogHydrator($translator);
    }
}
