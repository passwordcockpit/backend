<?php

/**
 * DeleteFolderAction
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
use Mezzio\Hal\ResourceGenerator;

/**
 * @OA\Delete(
 *     path="/v1/folders/{folderId}",
 *     summary="Deletes a folder",
 *     description="",
 *     operationId="deleteFolder",
 *     tags={"folders"},
 *     @OA\Parameter(
 *         description="Folder id to delete",
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
 *         description="Folder not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class DeleteFolderAction implements RequestHandlerInterface
{
    /**
     *
     * @var FolderFacade
     */
    protected $folderFacade;

    /**
     *
     * @var ResourceGenerator
     */
    protected $halResourceGenerator;

    /**
     * Constructor
     *
     * @param FolderFacade $folderFacade
     * @param ResourceGenerator $halResourceGenerator
     */
    public function __construct(
        FolderFacade $folderFacade,
        ResourceGenerator $halResourceGenerator
    ) {
        $this->folderFacade = $folderFacade;
        $this->halResourceGenerator = $halResourceGenerator;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $result = $this->folderFacade->deleteFolder(
            $request->getAttribute('id')
        );
        if ($result) {
            return new \Laminas\Diactoros\Response\EmptyResponse();
        }
    }
}
