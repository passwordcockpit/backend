<?php

/**
 * GetFolderUserAction
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
use Folder\Api\V1\Facade\FolderUserFacade;
use Mezzio\Hal\ResourceGenerator;
use Laminas\I18n\Translator\Translator;
use Mezzio\Hal\HalResponseFactory;

/**
 * @OA\Get(
 *     path="/v1/folders/{folderId}/users/{userId}",
 *     summary="Get user access type to specified folder",
 *     description="Returns user access type on the specified folder by its id",
 *     operationId="getFolderUser",
 *     tags={"folders"},
 *     @OA\Parameter(
 *         description="Folder id",
 *         in="path",
 *         name="folderId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Parameter(
 *         description="User id",
 *         in="path",
 *         name="userId",
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
 *         response=204,
 *         description="No Content"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class GetFolderUserAction implements RequestHandlerInterface
{
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
        protected FolderUserFacade $folderUserFacade,
        protected FolderFacade $folderFacade,
        protected UserFacade $userFacade,
        protected Translator $translator, 
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory
    ){}

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
