<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Firebase\JWT\JWT;
use Zend\Crypt\Password\Bcrypt;
use Doctrine\ORM\EntityManager;
use User\Api\V1\Entity\User;
use App\Service\ProblemDetailsException;
use Zend\I18n\Translator\Translator;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use User\Api\V1\Hydrator\UserPermissionHydrator;
use Zend\Authentication\Adapter\Ldap;
use Firebase\JWT\BeforeValidException;
use Firebase\JWT\ExpiredException;
use Slim\Middleware\JwtAuthentication;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;
use Authorization\Api\V1\Facade\TokenUserFacade;

/**
 *
 * @SWG\Post(
 *     path="/auth/update",
 *     tags={"authentication"},
 *     operationId="UpdateToken",
 *     summary="Update Token",
 *     description="return an updated token if old token is valid",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Response(
 *         response=200,
 *         description="Ok"
 *     ),
 *     security={
 *       {"bearerAuth": {}}
 *     }
 * )
 *
 */

class AuthorizationUpdateToken implements RequestHandlerInterface
{
    /**
     *
     * @var ProblemDetailsresponseFactory
     */
    protected $problemDetailsFactory;

    /**
     *
     * @var mixin
     */
    private $config;

    /**
     *
     * @var TokenUserFacade
     */
    private $tokenUserFacade;

    public function __construct(
        ProblemDetailsResponseFactory $problemDetailsFactory,
        $config,
        TokenUserFacade $tokenUserfacade
    ) {
        $this->problemDetailsFactory = $problemDetailsFactory;
        $this->config = $config;
        $this->tokenUserFacade = $tokenUserfacade;
    }

    /**
     * Creates the updated JWT
     *
     * @param JwtAuthentication $authy
     * @param JWT $token
     * @return JWT
     */
    private function updateToken(JwtAuthentication $authy, $token)
    {
        // Current time for token
        $currentTime = new \DateTime();

        $future = new \DateTime("NOW");
        $expTime = $this->config['expiration_time'];
        $future->modify('+ ' . $expTime . ' minute');

        //$token->iat = $currentTime->getTimestamp();
        $token->exp = $future->getTimestamp();

        return JWT::encode($token, $authy->getSecret(), "HS256");
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $authy = new JwtAuthentication([
            "secret" => $this->config['secret_key']
        ]);

        $token = $authy->fetchToken($request);

        if ($token === null || $token === false) {
            $token = $request->getParsedBody()['token'];
        }
        $oldPayLoad = $authy->decodeToken($token);

        // invalid token
        if ($oldPayLoad === false) {
            $response = $this->problemDetailsFactory->createResponse(
                $request,
                400,
                'Token is invalid'
            );
            return $response;
        }

        // check if the token was issued more than 'hard_timeout' minutes ago.
        $hard_timeout = $this->config['hard_timeout'];
        $time_now = new \Datetime('NOW');
        $time_then = new \DateTime();
        $time_then->setTimestamp($oldPayLoad->iat);
        $interval = $time_now->diff($time_then);
        // hard timeout
        if (intval($interval->format("%i")) >= $hard_timeout) {
            $response = $this->problemDetailsFactory->createResponse(
                $request,
                400,
                'Token is invalid'
            );
            return $response;
        }

        $newToken = $this->updateToken($authy, $oldPayLoad);

        // switch to newToken in the correct tokenUser table
        $tokenUser = $this->tokenUserFacade->getByToken($token)[0];
        $this->tokenUserFacade->updateTokenUser($tokenUser, $newToken, false);

        return new JsonResponse(['token' => $newToken]);
    }
}
