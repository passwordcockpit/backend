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
use Authentication\Api\V1\Facade\TokenUserFacade;
use Laminas\Diactoros\Response\JsonResponse;

/**
 * @SWG\Delete(
 *     path="/api/v1/token/logout",
 *     tags={"authentication"},
 *     operationId="Logout",
 *     summary="Delete token in tokenUser table",
 *     description="When user logs out token is invalidated",
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

class AuthenticationLogout implements RequestHandlerInterface
{
    /**
     *
     * @var TokenUserFacade
     */
    private $tokenUserFacade;

    public function __construct(TokenUserFacade $tokenUserFacade)
    {
        $this->tokenUserFacade = $tokenUserFacade;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ProblemDetailsException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $token = $request->getAttribute("token", false);
        $userId = $token['sub'];

        // getting the user making the request
        $tokenUser = $this->tokenUserFacade->getByUserId($userId)[0];

        // delete token from the tokenUser table
        $this->tokenUserFacade->deleteToken($tokenUser);

        return new JsonResponse(['message' => "Logout successful"]);
    }
}
