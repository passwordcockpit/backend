<?php

/**
 * container-interop-doctrine
 *
 * @link      http://github.com/DASPRiD/container-interop-doctrine For the canonical source repository
 * @copyright 2016 Ben Scholzen 'DASPRiD'
 * @license   http://opensource.org/licenses/BSD-2-Clause Simplified BSD License
 */

namespace App\Service;

use Zend\Expressive\Helper\ServerUrlMiddleware;
use Zend\Expressive\Helper\UrlHelperMiddleware;
use App\Middleware\OptionsMiddleware;
use Zend\Expressive\Middleware\ImplicitHeadMiddleware;
use Zend\Expressive\Helper\BodyParams\BodyParamsMiddleware;
use Zend\Stratigility\Middleware\NotFoundHandler;
use Zend\Stratigility\Middleware\ErrorHandler;
use Psr\Container\ContainerInterface;
use Authentication\Api\V1\Middleware\AuthenticationMiddleware;
use Authorization\Api\V1\Middleware\AuthorizationMiddleware;
use Zend\Expressive\Router\Middleware\RouteMiddleware;
use Zend\Expressive\Router\Middleware\DispatchMiddleware;
use Tuupola\Middleware\JwtAuthentication;
use App\Middleware\I18nMiddleware;
use App\Middleware\CorsMiddleware;
use App\Middleware\StrictTransportSecurityMiddleware;
use App\Middleware\ContentSecurityMiddleware;
use Blast\BaseUrl\BaseUrlMiddleware;

class ApplicationDelegatorFactory
{
    /**
     * @param ContainerInterface $container
     * @param string $serviceName Name of the service being created.
     * @param callable $callback Creates and returns the service.
     * @return Application
     */
    public function __invoke(
        ContainerInterface $container,
        $serviceName,
        callable $callback
    ) {
        /** @var $app Application */
        $app = $callback();

        /**
         * Setup middleware pipeline:
         */
        $app->pipe(BaseUrlMiddleware::class);

        // Middlewares that adds security headers to each request.
        $app->pipe(StrictTransportSecurityMiddleware::class); // force https

        $app->pipe(CorsMiddleware::class); //this can be removed in prod since client is same origin as the server (and NOT localhost:4200 -> 10.0.3.150:4344)

        $app->pipe(ErrorHandler::class);
        $app->pipe(ServerUrlMiddleware::class);
        $app->pipe(BodyParamsMiddleware::class);

        $app->pipe(RouteMiddleware::class);

        $app->pipe(ImplicitHeadMiddleware::class);
        $app->pipe(OptionsMiddleware::class);
        $app->pipe(UrlHelperMiddleware::class);

        $app->pipe(JwtAuthentication::class);
        $app->pipe(ContentSecurityMiddleware::class); // disable external scripts

        $app->pipe(AuthenticationMiddleware::class);
        // Translator
        $app->pipe(I18nMiddleware::class);
        $app->pipe(AuthorizationMiddleware::class);

        $app->pipe(DispatchMiddleware::class);

        $app->pipe(NotFoundHandler::class);

        \Zend\Expressive\Container\ApplicationConfigInjectionDelegator::injectRoutesFromConfig(
            $app,
            $container->get('config')
        );

        return $app;
    }
}
