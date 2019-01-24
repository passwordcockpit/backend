<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace App;

use Slim\Middleware\JwtAuthentication;
use Zend\Expressive\Delegate;

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
                'Zend\Expressive\Delegate\DefaultDelegate' =>
                    Delegate\NotFoundDelegate::class
            ],
            'invokable' => [
                \Zend\Expressive\Helper\ServerUrlHelper::class =>
                    \Zend\Expressive\Helper\ServerUrlHelper::class,
                RouterInterface::class => ZendRouter::class
            ],
            'factories' => [
                Validator\StringParameterValidator::class =>
                    Factory\Validator\StringParameterValidatorFactory::class,
                Middleware\I18nMiddleware::class =>
                    Factory\I18nMiddlewareFactory::class,
                Middleware\StrictTransportSecurityMiddleware::class =>
                    Factory\StrictTransportSecurityFactory::class,
                Middleware\OptionsMiddleware::class =>
                    Factory\OptionsMiddlewareFactory::class,
                \Blast\BaseUrl\BaseUrlMiddleware::class =>
                    \Blast\BaseUrl\BaseUrlMiddlewareFactory::class,
                \Zend\Expressive\Application::class =>
                    \Zend\Expressive\Container\ApplicationFactory::class,
                \Zend\Expressive\Delegate\NotFoundDelegate::class =>
                    \Zend\Expressive\Container\NotFoundDelegateFactory::class,
                \Zend\Expressive\Helper\ServerUrlMiddleware::class =>
                    \Zend\Expressive\Helper\ServerUrlMiddlewareFactory::class,
                \Zend\Expressive\Helper\UrlHelper::class =>
                    \Zend\Expressive\Helper\UrlHelperFactory::class,
                \Zend\Expressive\Helper\UrlHelperMiddleware::class =>
                    \Zend\Expressive\Helper\UrlHelperMiddlewareFactory::class,
                \Zend\Stratigility\Middleware\ErrorHandler::class =>
                    \Zend\Expressive\Container\ErrorHandlerFactory::class,
                \Zend\Stratigility\Middleware\ErrorResponseGenerator::class =>
                    \Acelaya\ExpressiveErrorHandler\ErrorHandler\Factory\ContentBasedErrorResponseGeneratorFactory::class,
                \Zend\Stratigility\Middleware\NotFoundHandler::class =>
                    \Zend\ProblemDetails\ProblemDetailsNotFoundHandlerFactory::class,
                //Doctrine factory
                \Doctrine\ORM\EntityManagerInterface::class =>
                    \ContainerInteropDoctrine\EntityManagerFactory::class,
                \Zend\Expressive\Hal\Metadata\MetadataMap::class =>
                    Factory\DoctrineMetadataMapFactory::class,
                \Zend\I18n\Translator\Translator::class =>
                    \Zend\I18n\Translator\TranslatorServiceFactory::class
            ],
            'delegators' => [
                \Zend\Expressive\Application::class => [
                    Service\ApplicationDelegatorFactory::class
                ],
                JwtAuthentication::class => [
                    Delegator\DoublePassMiddlewareDelegator::class
                ]
            ]
        ];
    }

    public function getValidatorDependencies()
    {
        return [
            'factories' => [
                Validator\NoEntityExists::class =>
                    Factory\Validator\NoEntityExistsFactory::class
            ]
        ];
    }
}
