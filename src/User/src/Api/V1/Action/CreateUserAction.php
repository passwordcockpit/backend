<?php

/**
 * Create User Action
 *
 * @package User\Api\V1\Action
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Api\V1\Entity\User;
use Mezzio\Hal\HalResponseFactory;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\Hal\ResourceGenerator;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @OA\Post(
 *     path="/v1/users",
 *     tags={"users"},
 *     operationId="createUser",
 *     summary="Create a new user",
 *     description="",
 *     requestBody={"$ref": "#/components/requestBodies/CreateUserAction payload"},
 *     @OA\Response(
 *         response=201,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=405,
 *         description="Invalid input",
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 *    request="CreateUserAction payload",
 *    description="User object to create",
 *    required=true,
 *    @OA\Property(property="username", type="string", description="User's username"),
 *    @OA\Property(property="password", type="string", description="User's password"),
 *    @OA\Property(property="name", type="string", description="User's name"),
 *    @OA\Property(property="surname", type="string", description="User's surname"),
 *    @OA\Property(property="language", type="string", description="User's language"),
 *    @OA\Property(property="phone", type="string", description="User's phone number"),
 *    @OA\Property(property="email", type="string", description="User's email"),
 *    @OA\Property(property="enabled", type="boolean", description="Whether a user is enabled (true) or not (false)"),
 *    @OA\Property(property="change_password", type="boolean", description="Whether a user needs to change his password (true) or not (false)")
 * )
 */
class CreateUserAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param PermissionFacade $permissionFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected UserFacade $userFacade,
        protected PermissionFacade $permissionFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory
    ){}

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ProblemDetailsException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // create user
        $user = $this->userFacade->createUser($request);

        if ($user) {
            // create user permission  - set to false
            $this->permissionFacade->createUserPermission($user);

            $this->halResourceGenerator
                ->getMetadataMap()
                ->get(User::class)
                ->setRouteParams(['id' => $user->getUserId()]);

            $resource = $this->halResourceGenerator->fromObject(
                $user,
                $request
            );
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('User not created'),
                $this->translator->translate('Resource not created'),
                'https://httpstatus.es/404'
            );
        }
    }
}
