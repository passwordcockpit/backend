<?php declare(strict_types=1);
namespace App\Factory;

use App\Middleware\CorsMiddleware;
use Zend\Diactoros\Response;

class CorsMiddlewareFactory
{
    public function __invoke($container)
    {
        $addrConfig = $container->get('config')['client_address'];

        return new CorsMiddleware($addrConfig);
    }
}
