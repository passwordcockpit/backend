<?php

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Block all resources which do not respect the defined directives of Content-Security-Policy
 */
class ContentSecurityMiddleware implements MiddlewareInterface
{
    /**
     * Set of directives
     *
     *@var string
     */
    private $directives;

    /**
     * Constructor
     *
     * @param string
     */
    public function __construct($directives)
    {
        $this->directives = $directives;
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
        return $response->withAddedHeader(
            'Content-Security-Policy',
            $this->directives
        );
    }
}
