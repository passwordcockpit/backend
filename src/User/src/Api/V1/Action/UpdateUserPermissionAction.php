<?php

/**
 * UpdateUserPermissionAction
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
use User\Api\V1\Facade\PermissionFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\HalResponseFactory;
use User\Api\V1\Entity\Permission;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @SWG\Patch(
 *     path="/v1/users/{userId}/permissions",
 *     tags={"users"},
 *     operationId="updateUserPermissions",
 *     summary="Update user's permissions",
 *     description="Update user's permissions",
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
 *          @SWG\Schema(ref="#/definitions/UpdateUserPermissionAction payload")
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
 *		definition="UpdateUserPermissionAction payload",
 *		@SWG\Property(property="manage_users", type="boolean", description="Wether a user can manage other user (true) or not (false)"),
 *		@SWG\Property(property="create_folders", type="boolean", description="Wether a user can create root folders (true) or not (false)"),
 *		@SWG\Property(property="access_all_folders", type="boolean", description="Wether a user can access all folders (true) or not (false)"),
 *		@SWG\Property(property="view_logs", type="boolean", description="Wether a user can view logs (true) or not (false)")
 * )
 */
class UpdateUserPermissionAction implements RequestHandlerInterface
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
    private $halResourceGenerator;

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
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('id');
        $permission = $this->permissionFacade->updateUserPermission(
            $userId,
            $request
        );
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(Permission::class)
            ->setRouteParams(['id' => $userId]);
        $resource = $this->halResourceGenerator->fromObject(
            $permission,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
