<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Factory\Adapter;

use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Authentication\Api\V1\Adapter\DoctrineAdapter;

class DoctrineAdapterFactory
{
    public function __invoke(ContainerInterface $container)
    {
        /**
         * Invoke method, create instance of DoctrineAdapter class
         *
         * @param ContainerInterface $container
         * @return DoctrineAdapter
         */
        return new DoctrineAdapter(
            $container->get(EntityManagerInterface::class)
        );
    }
}
