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
use Slim\Middleware\JwtAuthentication;
use App\Middleware\I18nMiddleware;
use App\Middleware\CorsMiddleware;

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
        $app->pipe(\Blast\BaseUrl\BaseUrlMiddleware::class);
        $app->pipe(CorsMiddleware::class);

        $app->pipe(ErrorHandler::class);
        $app->pipe(ServerUrlMiddleware::class);
        $app->pipe(BodyParamsMiddleware::class);

        $app->pipe(RouteMiddleware::class);

        $app->pipe(ImplicitHeadMiddleware::class);
        $app->pipe(OptionsMiddleware::class);
        $app->pipe(UrlHelperMiddleware::class);

        $app->pipe(JwtAuthentication::class);
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
