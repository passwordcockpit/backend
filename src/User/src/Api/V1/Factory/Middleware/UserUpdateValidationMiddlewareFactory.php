<?php

/**
 * Description of UserValidationMiddlewareFactory
 *
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Factory\Middleware;

use Psr\Container\ContainerInterface;
use User\Api\V1\Middleware\UserValidationMiddleware;
use Zend\InputFilter\Factory as InputFilterFactory;
use Zend\InputFilter\InputFilterPluginManager;

class UserUpdateValidationMiddlewareFactory
{
    public function __invoke(ContainerInterface $container)
    {
        return new UserValidationMiddleware(
            new InputFilterFactory(
                $container->get(InputFilterPluginManager::class)
            ),
            $container->get('config')['locale'],
            $container->get("translator"),
            true
        );
    }
}
