<?php

/**
 * DeleteFolderUserAction
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
use Folder\Api\V1\Facade\FolderUserFacade;

/**
 * @OA\Delete(
 *     path="/v1/folders/{folderId}/users/{userId}",
 *     tags={"folders"},
 *     operationId="deleteUserAccessToFolder",
 *     summary="Delete access on a Folder to a User",
 *     description="Delete access on a Folder to a User",
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
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Invalid input",
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class DeleteFolderUserAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param FolderFacade $folderFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param UserFacade $useFacade
     * @param FolderUserFacade $folderUserFacade
     */
    public function __construct(
        protected FolderFacade $folderFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected UserFacade $userFacade,
        protected FolderUserFacade $folderUserFacade
    ){}

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $folder = $this->folderFacade->get($request->getAttribute('folderId'));
        $user = $this->userFacade->get($request->getAttribute('userId'));
        $this->folderUserFacade->deleteFolderUser($folder, $user);
        return new \Laminas\Diactoros\Response\EmptyResponse();
    }
}
