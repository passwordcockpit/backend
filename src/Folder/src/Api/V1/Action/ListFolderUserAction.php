<?php

/**
 * ListFolderUserAction
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
use Folder\Api\V1\Facade\FolderUserFacade;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Hal\HalResponseFactory;
use User\Api\V1\Collection\UserCollection;

/**
 * @OA\Get(
 *     path="/v1/folders/{folderId}/users",
 *     summary="Get users that have access to specified folder",
 *     description="Returns users that have access in the specified folder by its id",
 *     operationId="getFolderUsers",
 *     tags={"folders"},
 *     @OA\Parameter(
 *         description="Folder id where to get users with access",
 *         in="path",
 *         name="folderId",
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
class ListFolderUserAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param FolderUserFacade $folderUserFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected FolderUserFacade $folderUserFacade,
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
        $withoutRights = $request->getQueryParams()['without_rights'] ?? null;
        $users = $withoutRights
            ? $this->folderUserFacade->getUsersWithoutRights($folderId)
            : $this->folderUserFacade->getUsers($folderId);
        foreach ($users as $user) {
            $user->setFolderId($folderId);
            $user->setCompleteUser();
        }
        $usersArrayAdapter = new \Laminas\Paginator\Adapter\ArrayAdapter($users);
        $usersCollection = new UserCollection($usersArrayAdapter);
        $usersCollection->setDefaultItemCountPerPage(PHP_INT_MAX);
        $resource = $this->halResourceGenerator->fromObject(
            $usersCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
