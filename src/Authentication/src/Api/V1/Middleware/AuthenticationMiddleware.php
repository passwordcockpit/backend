<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Middleware;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\ProblemDetailsException;
use Psr\Http\Message\ResponseInterface;
use Zend\I18n\Translator\Translator;
use User\Api\V1\Facade\UserFacade;
use Authorization\Api\V1\Facade\TokenUserFacade;

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
     *
     * @var TokenUserFacade
     */
    private $tokenUserFacade;

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param UserFacade $userFacade
     */
    public function __construct(
        Translator $translator,
        UserFacade $userFacade,
        TokenUserFacade $tokenUserFacade
    ) {
        $this->translator = $translator;
        $this->userFacade = $userFacade;
        $this->tokenUserFacade = $tokenUserFacade;
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

        $tokenUser = $this->tokenUserFacade->getByUserId($userId)[0];
        $token1 = $request->getHeader("Authorization")[0];

        $tok = substr($token1, 7);
        // check if the token on the tokenUser table matches the one sent.
        if ($tokenUser == null || $tokenUser->getToken() != $tok) {
            throw new ProblemDetailsException(
                401,
                $this->translator->translate('Token is not valid'),
                $this->translator->translate("Invalid token"),
                'https://httpstatus.es/401'
            );
        }

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
