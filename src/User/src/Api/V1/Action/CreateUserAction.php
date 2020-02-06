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
 * @SWG\Post(
 *     path="/v1/users",
 *     tags={"users"},
 *     operationId="createUser",
 *     summary="Create a new user",
 *     description="",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="User object to create",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/CreateUserAction payload")
 *     ),
 *     @SWG\Response(
 *         response=201,
 *         description="OK",
 *     ),
 *     @SWG\Response(
 *         response=405,
 *         description="Invalid input",
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 * @SWG\Definition(
 *		definition="CreateUserAction payload",
 *		@SWG\Property(property="username", type="string", description="User's username"),
 *		@SWG\Property(property="password", type="string", description="User's password"),
 * 		@SWG\Property(property="name", type="string", description="User's name"),
 *		@SWG\Property(property="surname", type="string", description="User's surname"),
 *		@SWG\Property(property="language", type="string", description="User's language"),
 *		@SWG\Property(property="phone", type="string", description="User's phone number"),
 *		@SWG\Property(property="email", type="string", description="User's email"),
 *		@SWG\Property(property="enabled", type="boolean", description="Whether a user is enabled (true) or not (false)")
 *      @SWG\Property(property="change_password, type="boolean", description="Whether a user needs to change his password (true) or not (false)")
 * )
 */
class CreateUserAction implements RequestHandlerInterface
{
    /**
     *
     * @var UserFacade
     */
    protected $userFacade;

    /**
     *
     * @var PermissionFacade
     */
    protected $permissionFacade;

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
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param PermissionFacade $permissionFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        UserFacade $userFacade,
        PermissionFacade $permissionFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->userFacade = $userFacade;
        $this->permissionFacade = $permissionFacade;
        $this->halResourceGenerator = $halResourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
    }

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
