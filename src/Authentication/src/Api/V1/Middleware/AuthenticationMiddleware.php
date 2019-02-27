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
use Authentication\Api\V1\Facade\TokenUserFacade;
use Zend\Authentication\Adapter\AdapterInterface;

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
     *
     * @var AdapterInterface
     */
    private $authAdapter;

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param UserFacade $userFacade
     * @param AdapterInterface $authAdapter
     * @param TokenUserFacade $tokenUserFacade
     */
    public function __construct(
        Translator $translator,
        UserFacade $userFacade,
        TokenUserFacade $tokenUserFacade,
        AdapterInterface $authAdapter
    ) {
        $this->translator = $translator;
        $this->userFacade = $userFacade;
        $this->tokenUserFacade = $tokenUserFacade;
        $this->authAdapter = $authAdapter;
    }

    /**
     * Return true if it's a valid update user request to change password
     *
     * @param ServerRequestInterface $request
     * @param int $userId
     *
     * @return bool
     */
    public function isAllowedCall($request, $userId)
    {
        if (
            //update himself request
            //get himself request
            (($request->getMethod() == 'PUT' ||
                $request->getMethod() == 'PATCH') &&
                $request->getRequestTarget() == '/api/v1/users/' . $userId &&
                isset($request->getParsedBody()['actual_password'])) ||
            ($request->getMethod() == 'GET' &&
                $request->getRequestTarget() == '/api/v1/users/' . $userId) ||
            ($request->getMethod() == 'DELETE' &&
                $request->getRequestTarget() == '/api/v1/token/logout')
        ) {
            return true;
        }
        return false;
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

        $userId = $token['sub'];

        $user = $this->userFacade->get($userId);

        $tokenUser = $this->tokenUserFacade->getByUserId($userId)[0];
        //Get token from header because it's encoded
        $token1 = $request->getHeader("Authorization")[0];
        // Remove Bearer
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
        // check if user has still to change his password
        $changePass = $user->getChangePassword();

        // if user has still to change his password he cannot make request on endpoints
        // other than the one to update himself.
        if (
            //it's not a valid request to change password
            $changePass &&
            !$this->isAllowedCall($request, $userId) &&
            //it's not an ldap
            get_class($this->authAdapter) !=
                'Authentication\Api\V1\Adapter\LdapAdapter'
        ) {
            throw new ProblemDetailsException(
                401,
                sprintf(
                    $this->translator->translate(
                        'User %s has not changed his password'
                    ),
                    $user->getUsername()
                ),
                $this->translator->translate('User not authorized'),
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
