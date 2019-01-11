<?php

/**
 * CreateFolderAction
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
use Folder\Api\V1\Facade\FolderFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\HalResponseFactory;
use Folder\Api\V1\Entity\Folder;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @SWG\Post(
 *     path="/v1/folders",
 *     tags={"folders"},
 *     operationId="createFolder",
 *     summary="Create a new Folder",
 *     description="Create a new Folder",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="body",
 *         in="body",
 *         description="Folder object to create",
 *         required=true,
 *         @SWG\Schema(ref="#/definitions/CreateFolderAction payload")
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
 *		definition="CreateFolderAction payload",
 * 		@SWG\Property(property="name", type="string", description="Name of the folder"),
 *      @SWG\Property(property="folder_id", type="integer", description="Id of the parent folder"),
 * )
 */
class CreateFolderAction implements RequestHandlerInterface
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
     *
     * @var HalResponseFactory
     */
    protected $halResponseFactory;

    /**
     * Constructor
     *
     * @param FolderFacade $folderFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        FolderFacade $folderFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->halResourceGenerator = $halResourceGenerator;
        $this->folderFacade = $folderFacade;
        $this->halResponseFactory = $halResponseFactory;
    }

    /**
     * MiddlewareInterface handler
     *
     * This method is used to create a new folder, it returns an HAL message related to the created resource
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $folder = $this->folderFacade->create($request);
        $folderId = $folder->getFolderId();
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(Folder::class)
            ->setRouteParams(['id' => $folderId]);
        $resource = $this->halResourceGenerator->fromObject($folder, $request);
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
