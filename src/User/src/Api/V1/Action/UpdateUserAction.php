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
use Zend\Expressive\Hal\ResourceGenerator;
use User\Api\V1\Entity\User;
use Zend\Expressive\Hal\HalResponseFactory;
use Firebase\JWT\JWT;
use Tuupola\Middleware\JwtAuthentication;
use Authentication\Api\V1\Facade\TokenUserFacade;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @SWG\Patch(
 *     path="/v1/users/{userId}",
 *     tags={"users"},
 *     operationId="updateUser",
 *     summary="Update an existing user",
 *     description="Update an existing user",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         description="User id to update",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="User object that needs to be updated",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/UpdateUserAction payload")
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="OK"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 * @SWG\Definition(
 *		definition="UpdateUserAction payload",
 *		@SWG\Property(property="username", type="string", description="User's username"),
 *		@SWG\Property(property="password", type="string", description="User's password"),
 *		@SWG\Property(property="actual_password", type="string", description="User's actual password"),
 * 		@SWG\Property(property="name", type="string", description="User's name"),
 *		@SWG\Property(property="surname", type="string", description="User's surname"),
 *		@SWG\Property(property="language", type="string", description="User's language"),
 *		@SWG\Property(property="phone", type="string", description="User's phone number"),
 *		@SWG\Property(property="email", type="string", description="User's email"),
 *		@SWG\Property(property="enabled", type="boolean", description="Wether a user is enabled (true) or not (false)")
 * )
 */
class UpdateUserAction implements RequestHandlerInterface
{
    /**
     *
     * @var UserFacade
     */
    protected $userFacade;

    /**
     *
     * @var ResourceGenerator
     */
    protected $halResourceGenerator;

    /**
     *
     * @var HalResponseFactory
     */
    protected $halResponseFactory;

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
        UserFacade $userFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory,
        $config,
        TokenUserFacade $tokenUserFacade
    ) {
        $this->userFacade = $userFacade;
        $this->halResourceGenerator = $halResourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
        $this->config = $config;
        $this->tokenUserFacade = $tokenUserFacade;
    }

    // return a new token with the language changed
    // if language is changed, return updated token
    // if language is not changed, return normal token
    private function updateTokenSpecifics($request, $resource)
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
        $user = $this->userFacade->update($userId, $request);
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(User::class)
            ->setRouteParams(['id' => $user->getUserId()]);
        $resource = $this->halResourceGenerator->fromObject($user, $request);
        $resource = $this->updateTokenSpecifics($request, $resource);

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
