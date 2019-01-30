<?php

/**
 * @package App\Validator
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Factory\Validator;

use App\Validator\NoEntityExists;
use Psr\Container\ContainerInterface;
use Doctrine\ORM\EntityManagerInterface;

class NoEntityExistsFactory
{
    public function __invoke(
        ContainerInterface $container,
        $requestedName,
        array $options = null
    ) {
        return new NoEntityExists(
            $options,
            $container->get(EntityManagerInterface::class)
        );
    }
}
