<?php declare(strict_types=1);

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace App\Factory;

use App\Middleware\CorsMiddleware;
use Laminas\Diactoros\Response;

class CorsMiddlewareFactory
{
    public function __invoke($container)
    {
        /**
         * Invoke method, create instance of CorsMiddleware class
         *
         * @param ContainerInterface $container
         * @return CorsMiddleware
         */
        return new CorsMiddleware($container->get('config')['client_address']);
    }
}
