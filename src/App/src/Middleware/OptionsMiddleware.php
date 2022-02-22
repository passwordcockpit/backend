<?php
/**
 * @see       https://github.com/mezzio/mezzio for the canonical source repository
 * @copyright Copyright (c) 2016-2017 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   https://github.com/mezzio/mezzio/blob/master/LICENSE.md New BSD License
 */

namespace App\Middleware;

use Fig\Http\Message\RequestMethodInterface as RequestMethod;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Router\RouteResult;

class OptionsMiddleware implements MiddlewareInterface
{
    /**
     * @var callable
     */
    private $responseFactory;

    /**
     * @param callable $responseFactory A factory capable of returning an
     *     empty ResponseInterface instance to return for implicit OPTIONS
     *     requests.
     */
    public function __construct(callable $responseFactory)
    {
        // Factories is wrapped in a closure in order to enforce return type safety.
        $this->responseFactory = fn(): ResponseInterface => $responseFactory();
    }

    /**
     * Handle an implicit OPTIONS request.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler) : ResponseInterface
    {
        if ($request->getMethod() !== RequestMethod::METHOD_OPTIONS) {
            return $handler->handle($request);
        }

        $result = $request->getAttribute(RouteResult::class);
        if (! $result) {
            return $handler->handle($request);
        }

        if ($result->isFailure() && ! $result->isMethodFailure()) {
            return $handler->handle($request);
        }

        if ($result->getMatchedRoute()) {
            return $handler->handle($request);
        }

        $allowedMethods = $result->getAllowedMethods();
        return ($this->responseFactory)()->withHeader('Access-Control-Allow-Methods', implode(',', $allowedMethods));
    }
}
