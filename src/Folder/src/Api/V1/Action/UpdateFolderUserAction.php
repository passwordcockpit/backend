<?php

/**
 * UpdateFolderUserAction
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Folder\Api\V1\Facade\FolderFacade;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGenerator;
use Folder\Api\V1\Entity\FolderUser;
use Mezzio\Hal\HalResponseFactory;
use Folder\Api\V1\Facade\FolderUserFacade;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @OA\Patch(
 *     path="/v1/folders/{folderId}/users/{userId}",
 *     tags={"folders"},
 *     operationId="updateUserAccessToFolder",
 *     summary="Update access on a Folder to a User",
 *     description="Update access on a Folder to a User",
 *     @OA\Parameter(
 *         description="Folder id",
 *         in="path",
 *         name="folderId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Parameter(
 *         description="User id",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/UpdateFolderUserAction payload"},
 *     @OA\Response(
 *         response=201,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Invalid input",
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 *		request="UpdateFolderUserAction payload",
 *    description="Access right for user on folder",
 *    required=true,
 * 		@OA\Property(property="access", type="integer", description="1: Read or 2: Manage folder", example=1)
 * )
 */
class UpdateFolderUserAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param FolderFacade $folderFacade
     * @param UserFacade $userFacade
     * @param FolderUserFacade $folderUserFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected FolderFacade $folderFacade,
        protected UserFacade $userFacade,
        protected FolderUserFacade $folderUserFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory
    ){}

    /**
     * MiddlewareInterface handler
     *
     * Update access type of user # on folder #
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $folder = $this->folderFacade->get($request->getAttribute('folderId'));
        $user = $this->userFacade->get($request->getAttribute('userId'));
        $access = $request->getParsedBody()['access'];
        $folderUser = $this->folderUserFacade->updateUserOnFolder(
            $folder,
            $user,
            $access
        );
        if ($folderUser) {
            $folderId = $folderUser->getFolder()->getFolderId();
            $userId = $folderUser->getUser()->getUserId();
            $this->halResourceGenerator
                ->getMetadataMap()
                ->get(FolderUser::class)
                ->setRouteParams([
                    'folderId' => $folderId,
                    'userId' => $userId
                ]);
            $resource = $this->halResourceGenerator->fromObject(
                $folderUser,
                $request
            );
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        }
    }
}
