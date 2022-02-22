<?php

/**
 * GetFolderAction
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
use Folder\Api\V1\Facade\FolderUserFacade;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Hal\HalResponseFactory;
use Laminas\Diactoros\Response\EmptyResponse;

/**
 * @OA\Get(
 *     path="/v1/folders/{folderId}",
 *     summary="Get a folder",
 *     description="Returns a folder by its id",
 *     operationId="getFolder",
 *     tags={"folders"},
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
class GetFolderAction implements RequestHandlerInterface
{
    /**
     *
     * @var FolderUserFacade
     */
    protected $folderUserFacade;

    /**
     *
     * @var FolderFacade
     */
    protected $folderFacade; //oggetto della classe FolderFacade

    /**
     *
     * @var ResourceGenerator
     */
    protected $halResourceGenerator;

    /**
     *
     * @var HalResponseFactory
     */
    private $halResponseFactory;

    /**
     * Constructor
     *
     * @param FolderUserFacade $foldrUserFacade
     * @param FolderFacade $folderFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        FolderUserFacade $folderUserFacade,
        FolderFacade $folderFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->folderUserFacade = $folderUserFacade;
        $this->halResourceGenerator = $halResourceGenerator;
        $this->folderFacade = $folderFacade;
        $this->halResponseFactory = $halResponseFactory;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $folder = $this->folderFacade->get($request->getAttribute('id'));
        if ($folder) {
            $user = $request->getAttribute('Authentication\User');
            $folderUser = $this->folderUserFacade->getFolderUsers(
                $folder,
                $user
            );
            $folderUser
                ? $folder->setAccess($folderUser->getAccess())
                : $folder->setAccess(null);
            return $this->halResponseFactory->createResponse(
                $request,
                $this->halResourceGenerator->fromObject($folder, $request)
            );
        }
        return new EmptyResponse();
    }
}
