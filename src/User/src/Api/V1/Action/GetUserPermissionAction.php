<?php

/**
 * GetUserRightAction
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
use Mezzio\Hal\HalResponseFactory;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\Hal\ResourceGenerator;

/**
 * @OA\Get(
 *     path="/v1/users/{userId}/permissions",
 *     summary="Get user's permissions",
 *     description="Returns permissions for user by its id",
 *     operationId="getUserPermissions",
 *     tags={"users"},
 *     @OA\Parameter(
 *         description="User id to fetch",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Not Found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class GetUserPermissionAction implements RequestHandlerInterface
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
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     * @param PermissionFacade $permissionFacade
     */
    public function __construct(
        UserFacade $userFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory,
        PermissionFacade $permissionFacade
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
        $permission = $this->permissionFacade->getUserPermission(
            $request->getAttribute('id')
        );
        if ($permission) {
            $resource = $this->halResourceGenerator->fromObject(
                $permission,
                $request
            );
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        }
        return new \Laminas\Diactoros\Response\EmptyResponse();
    }
}
