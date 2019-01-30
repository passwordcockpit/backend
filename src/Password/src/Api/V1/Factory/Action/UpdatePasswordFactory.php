<?php

/**
 * @see https://github.com/passwordcockpitackend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpitackend/blob/master/LICENSE.md BSD 3-Clause License
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
        $halResourceGenerator = new ResourceGeneratorFactory();

        return new UpdatePasswordAction(
            $container->get(PasswordFacade::class),
            $container->get(ProblemDetailsResponseFactory::class),
            $container->get('config')['authentication'],
            $halResourceGenerator($container),
            $container->get(\Zend\Expressive\Hal\HalResponseFactory::class)
        );
    }
}
