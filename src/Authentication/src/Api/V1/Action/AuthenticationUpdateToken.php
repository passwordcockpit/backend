<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Service\ProblemDetailsException;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;
use Authentication\Api\V1\Facade\TokenUserFacade;

/**
 *
 * @OA\Post(
 *     path="/api/v1/token/update",
 *     tags={"authentication"},
 *     operationId="UpdateToken",
 *     summary="Update Token",
 *     description="return an updated token if old token is valid",
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *         @OA\JsonContent()
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 *
 */
class AuthenticationUpdateToken implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param ProblemDetailsFactory $problemDetailsFactory
     * @param array $config
     * @param TokenUserFacade $tokenUserFacade
     */
    public function __construct(
      protected ProblemDetailsResponseFactory $problemDetailsFactory,
      private array $config,
      private readonly TokenUserFacade $tokenUserFacade
    ){}

    /**
     * Creates the updated JWT
     *
     * @param JWT $token
     * @return JWT
     */
    private function updateToken($token)
    {
        $future = new \DateTime("NOW");
        $expTime = $this->config['expiration_time'];
        $future->modify('+ ' . $expTime . ' minute');

        //$token->iat = $currentTime->getTimestamp();
        $token->exp = $future->getTimestamp();

        return JWT::encode((array) $token, $this->config['secret_key'], "HS256");
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ProblemDetailsException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        if (!isset($request->getParsedBody()['token'])) {
            throw new ProblemDetailsException(
                401,
                'Token is not valid',
                "Invalid token",
                'https://httpstatus.es/401'
            );
        }

        $token = $request->getParsedBody()['token'];

        try {
            $oldPayLoad = JWT::decode($token, new Key($this->config['secret_key'], 'HS256'));
        } catch (\Exception) {
            throw new ProblemDetailsException(
                401,
                'Token is not valid',
                "Invalid token",
                'https://httpstatus.es/401'
            );
        }

        // Invalid token
        if ($oldPayLoad === false) {
            $response = $this->problemDetailsFactory->createResponse(
                $request,
                401,
                'Token is invalid'
            );
            return $response;
        }

        // Check if the token was issued more than 'hard_timeout' minutes ago.
        $hard_timeout = $this->config['hard_timeout'];
        $time_now = new \Datetime('NOW');
        $time_then = new \DateTime();
        $time_then->setTimestamp($oldPayLoad->iat);
        $interval = $time_now->diff($time_then);
        // Hard timeout
        if (intval($interval->format("%i")) >= $hard_timeout) {
            $response = $this->problemDetailsFactory->createResponse(
                $request,
                401,
                'Token is invalid'
            );
            return $response;
        }

        $newToken = $this->updateToken($oldPayLoad);

        // Switch to newToken in the correct tokenUser table
        $tokenUser = $this->tokenUserFacade->getByToken($token)[0];
        $this->tokenUserFacade->updateTokenUser($tokenUser, $newToken, false);

        return new JsonResponse(['token' => $newToken]);
    }
}
