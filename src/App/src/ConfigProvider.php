<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App;

use Tuupola\Middleware\JwtAuthentication;
use Mezzio\Delegate;

/**
 * The configuration provider for the App module
 *
 * @see https://docs.zendframework.com/zend-component-installer/
 */
class ConfigProvider
{
    /**
     * Returns the configuration array
     *
     * To add a bit of a structure, each section is defined in a separate
     * method which returns an array with its configuration.
     *
     * @return array
     */
    public function __invoke()
    {
        return [
            'dependencies' => $this->getDependencies(),
            'validators' => $this->getValidatorDependencies()
        ];
    }

    /**
     * Returns the container dependencies
     *
     * @return array
     */
    public function getDependencies()
    {
        return [
            'aliases' => [
                'Mezzio\Delegate\DefaultDelegate' => Delegate\NotFoundDelegate::class
            ],
            'invokable' => [
                \Mezzio\Helper\ServerUrlHelper::class => \Mezzio\Helper\ServerUrlHelper::class,
                RouterInterface::class => LaminasRouter::class
            ],
            'factories' => [
                Validator\StringParameterValidator::class => Factory\Validator\StringParameterValidatorFactory::class,
                Middleware\I18nMiddleware::class => Factory\I18nMiddlewareFactory::class,
                Middleware\StrictTransportSecurityMiddleware::class => Factory\StrictTransportSecurityFactory::class,
                Middleware\ContentSecurityMiddleware::class => Factory\ContentSecurityFactory::class,
                Middleware\OptionsMiddleware::class => Factory\OptionsMiddlewareFactory::class,
                \Blast\BaseUrl\BaseUrlMiddleware::class => \Blast\BaseUrl\BaseUrlMiddlewareFactory::class,
                \Mezzio\Application::class => \Mezzio\Container\ApplicationFactory::class,
                \Mezzio\Delegate\NotFoundDelegate::class => \Mezzio\Container\NotFoundDelegateFactory::class,
                \Mezzio\Helper\ServerUrlMiddleware::class => \Mezzio\Helper\ServerUrlMiddlewareFactory::class,
                \Mezzio\Helper\UrlHelper::class => \Mezzio\Helper\UrlHelperFactory::class,
                \Mezzio\Helper\UrlHelperMiddleware::class => \Mezzio\Helper\UrlHelperMiddlewareFactory::class,
                \Laminas\Stratigility\Middleware\ErrorHandler::class => \Mezzio\Container\ErrorHandlerFactory::class,
                \Laminas\Stratigility\Middleware\ErrorResponseGenerator::class => \Acelaya\ExpressiveErrorHandler\ErrorHandler\Factory\ContentBasedErrorResponseGeneratorFactory::class,
                \Laminas\Stratigility\Middleware\NotFoundHandler::class => \Mezzio\ProblemDetails\ProblemDetailsNotFoundHandlerFactory::class,
                Middleware\CorsMiddleware::class => Factory\CorsMiddlewareFactory::class,
                //Doctrine factory
                \Doctrine\ORM\EntityManagerInterface::class => \ContainerInteropDoctrine\EntityManagerFactory::class,
                \Mezzio\Hal\Metadata\MetadataMap::class => Factory\DoctrineMetadataMapFactory::class,
                \Laminas\I18n\Translator\Translator::class => \Laminas\I18n\Translator\TranslatorServiceFactory::class
            ],
            'delegators' => [
                \Mezzio\Application::class => [
                    Service\ApplicationDelegatorFactory::class
                ]
            ]
        ];
    }

    public function getValidatorDependencies()
    {
        return [
            'factories' => [
                Validator\NoEntityExists::class => Factory\Validator\NoEntityExistsFactory::class
            ]
        ];
    }
}
