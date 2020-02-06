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
 * @SWG\Patch(
 *     path="/v1/folders/{folderId}/users/{userId}",
 *     tags={"folders"},
 *     operationId="updateUserAccessToFolder",
 *     summary="Update access on a Folder to a User",
 *     description="Update access on a Folder to a User",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         description="Folder id",
 *         in="path",
 *         name="folderId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Parameter(
 *         description="User id",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="Access right for user on folder",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/UpdateFolderUserAction payload")
 *     ),
 *     @SWG\Response(
 *         response=201,
 *         description="OK",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Invalid input",
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 * @SWG\Definition(
 *		definition="UpdateFolderUserAction payload",
 * 		@SWG\Property(property="access", type="integer", description="1: Read or 2: Manage folder", example=1)
 * )
 */
class UpdateFolderUserAction implements RequestHandlerInterface
{
    /**
     *
     * @var FolderFacade
     */
    protected $folderFacade;

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
     * @var FolderUserFacade
     */
    protected $folderUserFacade;

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
        FolderFacade $folderFacade,
        UserFacade $userFacade,
        FolderUserFacade $folderUserFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->folderFacade = $folderFacade;
        $this->userFacade = $userFacade;
        $this->folderUserFacade = $folderUserFacade;
        $this->halResourceGenerator = $halResourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
    }

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
