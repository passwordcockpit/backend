<?php

/**
 * ListFolderAction
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
use Folder\Api\V1\Collection\FolderCollection;
use Mezzio\Hal\ResourceGenerator;
use Laminas\Paginator\Adapter\ArrayAdapter;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\Hal\HalResponseFactory;

/**
 * @SWG\Get(
 *     path="/v1/folders",
 *     summary="List folders",
 *     description="Returns the list of folders. Use ?q=... to search for specific folders.",
 *     operationId="listFolders",
 *     produces={"application/json"},
 *     tags={"folders"},
 *     @SWG\Response(
 *         response=200,
 *         description="OK"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class ListFolderAction implements RequestHandlerInterface
{
    /**
     *
     * @var FolderFacade
     */
    protected $folderFacade; //oggetto della classe FolderFacade

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
    protected $halResourceGenerator;

    /**
     *
     * @var HalResponseFactory
     */
    protected $halResponseFactory;

    /**
     * Constructor
     *
     * @param FolderFacade $folderFacade
     * @param UserFacade $userFacade
     * @param PermissionFacade $permissionFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        FolderFacade $folderFacade,
        UserFacade $userFacade,
        PermissionFacade $permissionFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->folderFacade = $folderFacade;
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
        $queryParams = $request->getQueryParams();

        $queryParam = "";
        // checks if there are params
        if (sizeof($queryParams) > 0 && isset($queryParams['q'])) {
            $queryParam = $queryParams['q'];
        }

        $user = $request->getAttribute('Authentication\User');
        $roles = $this->permissionFacade->getUserPermissionArray($user);
        $accessAllFolders = in_array('access_all_folders', $roles['roles']);
        // check 'access_all_folders' permission
        if ($accessAllFolders) {
            $folders = $this->folderFacade->getAllByName($queryParam);
            $folders = $this->folderFacade->setAccessToFolders(
                $folders,
                $user->getUserId()
            );
        } else {
            $folders = $this->folderFacade->getByName(
                $queryParam,
                $user->getUserId()
            );
            if (sizeof($queryParams) == 0) {
                $folders = $this->folderFacade->generateTree($folders);
            }
            //$folders = $this->folderFacade->generateTree($folders);
            $folders = $this->folderFacade->setAccessToFolders(
                $folders,
                $user->getUserId()
            );
            // order folders by name
            usort($folders, function ($a, $b) {
                return strcasecmp($a->getName(), $b->getName());
            });
        }
        $foldersCollection = new FolderCollection(new ArrayAdapter($folders));
        $foldersCollection->setDefaultItemCountPerPage(PHP_INT_MAX);
        $resource = $this->halResourceGenerator->fromObject(
            $foldersCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
