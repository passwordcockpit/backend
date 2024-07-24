<?php

/**
 * I18nMiddleware
 *
 * @package App\Middleware
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Aron Castellani <aron.castellani@blackpoints.ch>
 */

namespace App\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\I18n\Translator\Resources;
use Laminas\I18n\Translator\Translator;
use Laminas\Validator\AbstractValidator;

class I18nMiddleware implements MiddlewareInterface
{
    /**
     *
     * @param Translator $translator
     * @param array $languages
     */
    public function __construct(private readonly Translator $translator, private array $languages)
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
        $user = $request->getAttribute('Authentication\User');
        $serverParams = $request->getServerParams();
        if (
            isset($serverParams['HTTP_ACCEPT_LANGUAGE']) &&
            in_array($serverParams['HTTP_ACCEPT_LANGUAGE'], $this->languages)
        ) {
            // Set locale based on the desired client's language
            $locale = $serverParams['HTTP_ACCEPT_LANGUAGE'];
        } elseif ($user) {
            // Set locale based on the user's language
            $locale = $user->getLanguage();
        } else {
            // Set default locale retrieving the first available language
            $locale = array_shift($this->languages);
        }

        $this->translator->setLocale($locale);
        $this->translator->addTranslationFilePattern(
            'phpArray',
            Resources::getBasePath(),
            Resources::getPatternForValidator()
        );

        //AbstractValidator::setDefaultTranslator($this->translator);
        $response = $handler->handle($request);
        return $response->withHeader(
            'Content-Language',
            $locale . "_" . strtoupper((string) $locale)
        );
    }
}
