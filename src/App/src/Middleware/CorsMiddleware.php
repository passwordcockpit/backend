<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * Handles CORS headers
 */
class CorsMiddleware implements MiddlewareInterface
{
    private $clientAddress;

    public function __construct($clientAddress)
    {
        $this->clientAddress = $clientAddress;
    }
    /**
     * Handle an implicit OPTIONS request.
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
        return $response
            ->withAddedHeader(
                'Access-Control-Allow-Origin',
                $this->clientAddress['address']
            )
            ->withAddedHeader(
                "Access-Control-Allow-Headers",
                "Authorization, Content-Type, Accept"
            );
        //->withAddedHeader('Access-Control-Allow-Origin', 'http://localhost:4200')
    }
}
