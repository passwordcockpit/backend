<?php

/**
 * ListFolderPasswordAction
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
use Password\Api\V1\Collection\PasswordCollection;
use Password\Api\V1\Facade\PasswordFacade;

/**
 * @SWG\Get(
 *     path="/v1/folders/{folderId}/passwords",
 *     summary="Get passwords in specified folder",
 *     description="Returns passwords in the specified folder by its id",
 *     operationId="getFolderPassword",
 *     produces={"application/json"},
 *     tags={"folders"},
 *     @SWG\Parameter(
 *         description="Folder id where to get passwords",
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
class ListFolderPasswordAction implements RequestHandlerInterface
{
    /**
     *
     * @var FolderFacade
     */
    protected $folderFacade;

    /**
     *
     * @var PasswordFacade
     */
    protected $passwordFacade;

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
     */
    public function __construct(
        FolderFacade $folderFacade,
        PasswordFacade $passwordFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->folderFacade = $folderFacade;
        $this->passwordFacade = $passwordFacade;
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
        $passwords = $this->passwordFacade->getPasswords($folderId);
        foreach ($passwords as $password) {
            $password->setCompletePassword();
        }
        $passwordsArrayAdapter = new \Zend\Paginator\Adapter\ArrayAdapter(
            $passwords
        );
        $passwordsCollection = new PasswordCollection($passwordsArrayAdapter);
        $passwordsCollection->setDefaultItemCountPerPage(PHP_INT_MAX);
        $resource = $this->halResourceGenerator->fromObject(
            $passwordsCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
