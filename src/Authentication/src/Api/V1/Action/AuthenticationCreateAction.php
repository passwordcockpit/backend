<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Authentication\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Diactoros\Response\JsonResponse;
use Firebase\JWT\JWT;
use User\Api\V1\Entity\User;
use App\Service\ProblemDetailsException;
use Zend\I18n\Translator\Translator;
use Zend\Authentication\Adapter\AdapterInterface;
use Zend\Authentication\Result;
use Authentication\Api\V1\Facade\TokenUserFacade;
use Authentication\Api\V1\Facade\LoginRequestFacade;

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
     * @var TokenUserFacade
     */
    private $tokenUserFacade;

    /**
     *
     * @var LoginRequestFacade
     */
    private $loginRequestFacade;

    /**
     *
     * @param array $config
     * @param Translator $translator
     * @param AdapterInterface $adapterInterface
     * @param TokenUserFacade $tokenUserFacade
     * @param LoginRequestFacade $loginRequestFacade
     */
    public function __construct(
        $config,
        Translator $translator,
        AdapterInterface $authAdapter,
        TokenUserFacade $tokenUserFacade,
        LoginRequestFacade $loginRequestFacade
    ) {
        $this->config = $config;
        $this->translator = $translator;
        $this->authAdapter = $authAdapter;
        $this->tokenUserFacade = $tokenUserFacade;
        $this->loginRequestFacade = $loginRequestFacade;
    }

    /**
     * This function handles the TokenUserTable after a successful login.
     * It need to map the user with his associated token
     *
     * @param User $user
     * @param string $token
     *
     * @return bool $firstTimeLogin
     */
    private function updateTokenUserTable($user, $token)
    {
        $userId = $user->getUserId();
        $tokenUser = $this->tokenUserFacade->getByUserId($userId);

        $firstTimeLogin = false;
        // FIRST TIME LOGIN, entry in the table does not exist!
        if ($tokenUser == null) {
            $this->tokenUserFacade->create($user, $token);
            if(get_class($this->authAdapter) ==
            'Authentication\Api\V1\Adapter\DoctrineAdapter'){
                $firstTimeLogin = true;
            }
            
        } else {
            //user already logged in. Modify token and date.
            if ($user->getChangePassword()) {
                // also if the user has not changed his password still
                $firstTimeLogin = true;
            }
            // since the tokenUser are returned as array we just need the first.
            $this->tokenUserFacade->updateTokenUser($tokenUser[0], $token);
        }
        return $firstTimeLogin;
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
                "ldap" => $isLdap,
                "change_password" => $user->getChangePassword()
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

        // -- IP CHECK FOR REQUESTS ---
        $timeAgo = new \DateTime("NOW", new \DateTimeZone('Europe/Zurich'));

        $timeAgo->modify('- ' . $this->config['attempt_timespan'] . ' minutes');

        $timeAgo = $timeAgo->format('Y-m-d H:i:s');

        // get how many failed attempts the ip did on the user the last hour
        $attempts = $this->loginRequestFacade->getLastAttempts(
            $_SERVER['REMOTE_ADDR'],
            $username,
            $timeAgo
        );

        if (sizeof($attempts) > $this->config['max_requests_per_timespan']) {
            throw new ProblemDetailsException(
                429,
                $this->translator->translate('Too many failed login attempts'),
                sprintf(
                    $this->translator->translate('Please wait %s minutes'),
                    $this->config['attempt_timespan']
                ),
                'https://httpstatus.es/429'
            );
        }

        $this->authAdapter->setUsername($username);
        $this->authAdapter->setPassword($password);

        $result = $this->authAdapter->authenticate();

        switch ($result->getCode()) {
            case Result::FAILURE_CREDENTIAL_INVALID:
                //need to log failed attempt
                $loginRequest = $this->loginRequestFacade->create([
                    "ip" => $_SERVER['REMOTE_ADDR'],
                    "username" => $username,
                    "attemptDate" => new \Datetime(
                        "now",
                        new \DateTimeZone('Europe/Zurich')
                    )
                ]);
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

                // create token
                $token = $this->createToken($user);

                // update the UserToken table, where user_id and token are stored.
                $firstTimeLogin = $this->updateTokenUserTable($user, $token);

                if ($firstTimeLogin) {
                    return new JsonResponse([
                        'token' => $token,
                        'firstTimeLogin' => true
                    ]);
                } else {
                    return new JsonResponse(['token' => $token]);
                }
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
