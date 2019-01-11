<?php

/**
 * GetFolderUserAction
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
use User\Api\V1\Facade\UserFacade;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Mvc\I18n\Translator;
use Zend\Expressive\Hal\HalResponseFactory;

/**
 * @SWG\Get(
 *     path="/v1/folders/{folderId}/users/{userId}",
 *     summary="Get user access type to specified folder",
 *     description="Returns user access type on the specified folder by its id",
 *     operationId="getFolderUser",
 *     produces={"application/json"},
 *     tags={"folders"},
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
 *     @SWG\Response(
 *         response=200,
 *         description="OK"
 *     ),
 *     @SWG\Response(
 *         response=204,
 *         description="No Content"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class GetFolderUserAction implements RequestHandlerInterface
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
    protected $folderFacade;

    /**
     *
     * @var UserFacade
     */
    protected $userFacade;

    /**
     *
     * @var Translator
     */
    protected $translator;

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
     * @param FolderFacade $folderFacade
     * @param UserFacade $userFacade
     * @param Translator $translator
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        FolderUserFacade $folderUserFacade,
        FolderFacade $folderFacade,
        UserFacade $userFacade,
        Translator $translator,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->folderUserFacade = $folderUserFacade;
        $this->folderFacade = $folderFacade;
        $this->userFacade = $userFacade;
        $this->translator = $translator;
        $this->halResourceGenerator = $halResourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
    }

    /**
     * MiddlewareInterface handler
     * Get permissions on folder # for user #
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $folder = $this->folderFacade->get($request->getAttribute('folderId'));
        $user = $this->userFacade->get($request->getAttribute('userId'));
        $folderUser = $this->folderUserFacade->getFolderUsers($folder, $user);
        if ($folderUser) {
            $resource = $this->halResourceGenerator->fromObject(
                $folderUser,
                $request
            );
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        }
        throw new \App\Service\ProblemDetailsException(
            404,
            sprintf(
                $this->translator->translate(
                    'Rights on folder %s not found for user %s'
                ),
                $folder->getFolderId(),
                $user->getUserId()
            ),
            $this->translator->translate('Resource not found'),
            'https://httpstatus.es/404'
        );
    }
}
