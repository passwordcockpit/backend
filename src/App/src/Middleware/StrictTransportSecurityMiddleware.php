<?php

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * Force https middleware
 */
class StrictTransportSecurityMiddleware implements MiddlewareInterface
{
    /**
     * @var int
     */
    private $maxAge;

    /**
     * Constructor
     *
     * @param array $config
     */
    public function __construct($config)
    {
        $this->maxAge = $config['security']['max-age'];
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
            'Strict-Transport-Security',
            "max-age=" . $this->maxAge
        );
    }
}
