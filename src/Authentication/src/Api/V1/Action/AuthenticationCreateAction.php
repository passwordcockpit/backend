<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Firebase\JWT\JWT;
use User\Api\V1\Entity\User;
use App\Service\ProblemDetailsException;
use Zend\Mvc\I18n\Translator;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;

/**
 *
 * @SWG\Post(
 *     path="/auth",
 *     tags={"authentication"},
 *     operationId="AuthenticationCreateAction",
 *     summary="Create a new token",
 *     description="",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="User credential",
 *         in="body",
 *         description="Credentials",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/AuthenticationCreateAction payload")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Ok",
 *     )
 * )
 * @SWG\Definition(
 * 			definition="AuthenticationCreateAction payload",
 * 			@SWG\Property(property="username", type="string", description="User identifier"),
 * 			@SWG\Property(property="password", type="string", description="User password"),
 * 		),
 */
class AuthenticationCreateAction implements RequestHandlerInterface
{
    /**
     *
     * @var mixin
     */
    private $config;

    /**
     *
     * @var Translator
     */
    private $translator;

    /**
     *
     * @var AdapterInterface
     */
    private $authAdapter;

    /**
     *
     * @param mixin $config
     */
    public function __construct(
        $config,
        Translator $translator,
        AdapterInterface $authAdapter
    ) {
        $this->config = $config;
        $this->translator = $translator;
        $this->authAdapter = $authAdapter;
    }

    /**
     * Creates the JWT
     *
     * @param User $user
     * @param array $data
     * @return JWT
     */
    private function createToken(User $user)
    {
        // Current time for token
        $currentTime = new \DateTime();
        // Expiration time in minutes
        if (isset($this->config['expiration_time'])) {
            $expirationTime = $this->config['expiration_time'];
        }
        // Secret key
        if (isset($this->config['secret_key'])) {
            $secretKey = $this->config['secret_key'];
        }
        // Is it an ldap authentication?
        $isLdap = false;
        if (
            get_class($this->authAdapter) ==
            'Authentication\Api\V1\Adapter\LdapAdapter'
        ) {
            $isLdap = true;
        }

        $tokenPayload = [
            "iat" => $currentTime->getTimestamp(),
            "exp" => $currentTime
                ->add(new \DateInterval('PT' . $expirationTime . 'M'))
                ->getTimestamp(),
            "data" => [
                "language" => $user->getLanguage(),
                "ldap" => $isLdap
            ],
            "sub" => $user->getUserId()
        ];
        return JWT::encode($tokenPayload, $secretKey);
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ProblemDetailsException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $payload = $request->getParsedBody();
        $username = $payload["username"];
        $password = $payload["password"];

        $this->authAdapter->setUsername($username);
        $this->authAdapter->setPassword($password);

        $result = $this->authAdapter->authenticate();

        switch ($result->getCode()) {
            case Result::FAILURE_CREDENTIAL_INVALID:
                throw new ProblemDetailsException(
                    401,
                    $this->translator->translate('Wrong username or password'),
                    $this->translator->translate('Unauthorized'),
                    'https://httpstatus.es/401'
                );
                break;

            case Result::FAILURE_IDENTITY_AMBIGUOUS:
                //USER NOT ENABLED
                $user = $result->getIdentity();
                throw new ProblemDetailsException(
                    401,
                    sprintf(
                        $this->translator->translate('User %s is not enabled'),
                        $user->getUsername()
                    ),
                    $this->translator->translate('Unauthorized'),
                    'https://httpstatus.es/401'
                );
                break;

            case Result::SUCCESS:
                $user = $result->getIdentity();
                $token = $this->createToken($user);
                return new JsonResponse(['token' => $token]);
                break;

            default:
                // other failures
                throw new ProblemDetailsException(
                    401,
                    $this->translator->translate('Wrong username or password'),
                    $this->translator->translate('Unauthorized'),
                    'https://httpstatus.es/401'
                );
                break;
        }
    }
}
