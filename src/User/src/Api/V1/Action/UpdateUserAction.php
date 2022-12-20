<?php

/**
 * UpdateUserAction
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGenerator;
use User\Api\V1\Entity\User;
use Mezzio\Hal\HalResponseFactory;
use Firebase\JWT\JWT;
use Authentication\Api\V1\Facade\TokenUserFacade;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @OA\Patch(
 *     path="/v1/users/{userId}",
 *     tags={"users"},
 *     operationId="updateUser",
 *     summary="Update an existing user",
 *     description="Update an existing user",
 *     @OA\Parameter(
 *         description="User id to update",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/UpdateUserAction payload"},
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 *		request="UpdateUserAction payload",
 *    description="User object that needs to be updated",
 *    required=true,
 *		@OA\Property(property="username", type="string", description="User's username"),
 *		@OA\Property(property="password", type="string", description="User's password"),
 *		@OA\Property(property="actual_password", type="string", description="User's actual password"),
 * 		@OA\Property(property="name", type="string", description="User's name"),
 *		@OA\Property(property="surname", type="string", description="User's surname"),
 *		@OA\Property(property="language", type="string", description="User's language"),
 *		@OA\Property(property="phone", type="string", description="User's phone number"),
 *		@OA\Property(property="email", type="string", description="User's email"),
 *		@OA\Property(property="enabled", type="boolean", description="Wether a user is enabled (true) or not (false)")
 * )
 */
class UpdateUserAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     * @param array $config
     * @param TokenUserFacade $tokenUserFacade
     */
    public function __construct(
        protected UserFacade $userFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory,
        private array $config,
        private readonly TokenUserFacade $tokenUserFacade
    ){}

    // return a new token with the language changed
    // if language is changed, return updated token
    // if language is not changed, return normal token
    private function updateTokenSpecifics($request, $resource, $user)
    {
        $token1 = $request->getHeader("Authorization")[0];
        $token = substr($token1, 7);
        $tokenUser = $this->tokenUserFacade->getByToken($token)[0];

        $payLoad = JWT::decode($token, $this->config['secret_key'], ["HS256"]);

        $specifics = $request->getParsedBody();

        foreach ($specifics as $spec => $value) {
            if ($spec === 'language' && $value != null) {
                $payLoad->data->$spec = $value;
                $token = JWT::encode(
                    $payLoad,
                    $this->config['secret_key'],
                    "HS256"
                );
            }
        }

        //update token on tokenUser table
        $this->tokenUserFacade->updateTokenUser($tokenUser, $token, false);
        // if user changed his password, token is deleted from tokenUser table forcing then a new login

        if (isset($specifics['actual_password'])) {
            $this->tokenUserFacade->deleteToken($tokenUser);
            if ($payLoad->data->change_password == true) {
                $this->userFacade->userChangedPassword($user);
            }
            $resource = $resource->withElement("forceLogin", "true");
            return $resource;
        }

        // ship token
        $resource = $resource->withElement("token", $token);
        return $resource;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('id');

        $user = $this->userFacade->updateUser($userId, $request);
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(User::class)
            ->setRouteParams(['id' => $user->getUserId()]);
        $resource = $this->halResourceGenerator->fromObject($user, $request);

        // if it's an admin that makes the changes, no need to updateTokenSpecifics.
        $token = $request->getAttribute("token", false);
        $userCalling = $token['sub'];
        if ($userCalling == $userId) {
            $resource = $this->updateTokenSpecifics($request, $resource, $user);
        }

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
