<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Action;

use Folder\Api\V1\Collection\FolderUserCollection;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Hal\HalResponseFactory;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGenerator;

/**
 * @OA\Get(
 *     path="/v1/users/{userId}/folders/permissions",
 *     summary="Get user permission on each folder (read or edit)",
 *     description="Returns user folder permissions",
 *     operationId="getUserFoldersPermission",
 *     tags={"users"},
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="No Content"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class GetUserFoldersPermissionAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     * @param array $paginatorConfig
     */
    public function __construct(
        protected UserFacade $userFacade,
        private readonly ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory,
        protected array $paginatorConfig
    ){}
    
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('id');
        $folders = $this->userFacade->listFoldersPermission($userId);

        $usersArrayAdapter = new \Laminas\Paginator\Adapter\ArrayAdapter($folders);
        $folderUserCollection = new FolderUserCollection($usersArrayAdapter);
        $folderUserCollection->setDefaultItemCountPerPage($this->paginatorConfig['small']);

        $this->halResourceGenerator->fromObject($folderUserCollection, $request);

        $resource = $this->halResourceGenerator->fromObject(
            $folderUserCollection,
            $request
        );

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
