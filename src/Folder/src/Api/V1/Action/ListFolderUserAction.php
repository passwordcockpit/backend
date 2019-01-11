<?php

/**
 * ListFolderUserAction
 *
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\HalResponseFactory;
use User\Api\V1\Collection\UserCollection;

/**
 * @SWG\Get(
 *     path="/v1/folders/{folderId}/users",
 *     summary="Get users that have access to specified folder",
 *     description="Returns users that have access in the specified folder by its id",
 *     operationId="getFolderUsers",
 *     produces={"application/json"},
 *     tags={"folders"},
 *     @SWG\Parameter(
 *         description="Folder id where to get users with access",
 *         in="path",
 *         name="folderId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="OK"
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Not Found"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class ListFolderUserAction implements RequestHandlerInterface
{
    /**
     *
     * @var FolderUserFacade
     */
    protected $folderUserFacade;

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
     * Constructor
     *
     * @param FolderUserFacade $folderUserFacade
     * @param ResourceGenerator $halResourceGenerator
     */
    public function __construct(
        FolderUserFacade $folderUserFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->folderUserFacade = $folderUserFacade;
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
        $folderId = $request->getAttribute('id');
        $withoutRights = $request->getQueryParams()['without_rights'] ?? null;
        $users = $withoutRights
            ? $this->folderUserFacade->getUsersWithoutRights($folderId)
            : $this->folderUserFacade->getUsers($folderId);
        foreach ($users as $user) {
            $user->setCompleteUser();
        }
        $usersArrayAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($users);
        $usersCollection = new UserCollection($usersArrayAdapter);
        $usersCollection->setDefaultItemCountPerPage(PHP_INT_MAX);
        $resource = $this->halResourceGenerator->fromObject(
            $usersCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
