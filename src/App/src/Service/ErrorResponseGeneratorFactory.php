<?php

/**
 * ErrorResponseGeneratorFactory
 *
 * @package App\Service
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace App\Service;

use Psr\Container\ContainerInterface;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

class ErrorResponseGeneratorFactory
{
    /**
     * @param ContainerInterface $container
     * @return ErrorResponseGenerator
     */
    public function __invoke(ContainerInterface $container)
    {
        return new ErrorResponseGenerator(
            $container->get(ProblemDetailsResponseFactory::class)
        );
    }
}
