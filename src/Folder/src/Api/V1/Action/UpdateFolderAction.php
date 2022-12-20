<?php

/**
 * UpdateFolderAction
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
use Folder\Api\V1\Entity\Folder;
use Folder\Api\V1\Facade\FolderFacade;
use Folder\Api\V1\Facade\FolderUserFacade;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Hal\HalResponseFactory;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @OS\Patch(
 *     path="/v1/folders/{folderId}",
 *     tags={"folders"},
 *     operationId="updateFolder",
 *     summary="Update a new Folder",
 *     description="Update a new Folder",
 *     @OA\Parameter(
 *         description="Folder id to fetch",
 *         in="path",
 *         name="folderId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/UpdateFolderAction payload"},
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Invalid input"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 *		request="UpdateFolderAction payload",
 *    description="Folder object to update",
 *    required=true,
 * 		@OA\Property(property="name", type="string", description="Name of the folder"),
 * )
 */
class UpdateFolderAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param FolderUserFacade $folderUserFacade
     * @param FolderFacade $folderFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected FolderUserFacade $folderUserFacade,
        protected FolderFacade $folderFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory
    ){}

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $folderId = $request->getAttribute('id');
        $folder = $this->folderFacade->updateFolder($folderId, $request);
        $user = $request->getAttribute('Authentication\User');
        $folderUser = $this->folderUserFacade->getFolderUsers($folder, $user);
        if ($folderUser) {
            $folder->setAccess($folderUser->getAccess());
        } else {
            $folder->setAccess(2);
        }
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(Folder::class)
            ->setRouteParams(['id' => $folder->getFolderId()]);
        $resource = $this->halResourceGenerator->fromObject($folder, $request);
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
