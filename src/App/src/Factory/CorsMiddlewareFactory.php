<?php declare(strict_types=1);
namespace App\Factory;

use App\Middleware\CorsMiddleware;
use Zend\Diactoros\Response;

class CorsMiddlewareFactory
{
    public function __invoke($container)
    {
        return new CorsMiddleware($container->get('config')['client_address']);
    }
}
