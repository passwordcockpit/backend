<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\ProblemDetailsException;
use Psr\Http\Message\ResponseInterface;
use Zend\Mvc\I18n\Translator;
use User\Api\V1\Facade\UserFacade;

class AuthenticationMiddleware implements MiddlewareInterface
{
    /**
     *
     * @var UserFacade
     */
    private $userFacade;

    /**
     *
     * @var Translator
     */
    private $translator;

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param UserFacade $userFacade
     */
    public function __construct(Translator $translator, UserFacade $userFacade)
    {
        $this->translator = $translator;
        $this->userFacade = $userFacade;
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
        $token = $request->getAttribute("token", false);
        if (!$token) {
            return $handler->handle($request);
        }

        $userId = $token->sub;
        $user = $this->userFacade->get($userId);

        //check if user is enabled
        $access = $user->getEnabled();
        if (!$access) {
            throw new ProblemDetailsException(
                401,
                sprintf(
                    $this->translator->translate('User %s is not enabled'),
                    $user->getUsername()
                ),
                $this->translator->translate('User not authorized'),
                'https://httpstatus.es/401'
            );
        } else {
            $request = $request->withAttribute('Authentication\User', $user);
            return $handler->handle($request);
        }
    }
}
