<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Factory\Action;

use Psr\Container\ContainerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Expressive\Hal\ResourceGeneratorFactory;
use Password\Api\V1\Action\UpdatePasswordAction;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

/**
 * Description of UpdatePasswordFactory
 */
class UpdatePasswordFactory
{
    public function __invoke(ContainerInterface $container)
    {
        $passwordFacade = $container->get(PasswordFacade::class);
        $problemDetailsFactory = $container->get(
            ProblemDetailsResponseFactory::class
        );
        $config = $container->get('config')['authentication'];
        $halResourceGenerator = new ResourceGeneratorFactory();
        $halResourceGeneratorInstance = $halResourceGenerator($container);
        $halResponseFactory = $container->get(
            \Zend\Expressive\Hal\HalResponseFactory::class
        );
        return new UpdatePasswordAction(
            $passwordFacade,
            $problemDetailsFactory,
            $config,
            $halResourceGeneratorInstance,
            $halResponseFactory
        );
    }
}
