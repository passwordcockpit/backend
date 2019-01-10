<?php declare(strict_types=1);
namespace App\Factory;
 
use Tuupola\Middleware\CorsMiddleware;
use Zend\Diactoros\Response;
use Zend\Stratigility\Middleware\CallableMiddlewareWrapper;
 
class CorsMiddlewareFactory
{
    public function __invoke($container)
    {
        return new CallableMiddlewareWrapper(
            new CorsMiddleware([
                "origin" => ["*"],
                "methods" => ["GET", "POST", "PUT", "PATCH", "DELETE"],
                "headers.allow" => ["Content-Type", "Accept"],
                "headers.expose" => [],
                "credentials" => false,
                "cache" => 0,
            ]),
            new Response()
        );
    }
}