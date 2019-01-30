<?php declare(strict_types=1);
namespace App\Factory;

use App\Middleware\CorsMiddleware;
use Zend\Diactoros\Response;

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
