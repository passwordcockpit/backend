<?php

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class ContentSecurityMiddleware implements MiddlewareInterface
{
    public function __construct()
    {
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $response = $handler->handle($request);
        //since we don't load external or internal scripts, we can set it to 'none'.
        return $response->withAddedHeader(
            'Content-Security-Policy',
            "default-src 'none'"
        );
    }
}
