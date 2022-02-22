<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Password\Api\V1\Action;

use App\Service\ProblemDetailsException;
use Folder\Api\V1\Facade\FolderUserFacade;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Mezzio\Hal\ResourceGenerator;
use Password\Api\V1\Entity\Password;
use User\Api\V1\Facade\PermissionFacade;
use User\Api\V1\Facade\UserFacade;
use Laminas\Diactoros\Response\JsonResponse;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\ProblemDetails\ProblemDetailsResponseFactory;

/**
 *
 * @OA\Patch(
 *     path="/v1/passwords",
 *     tags={"passwords"},
 *     operationId="updatePassword",
 *     summary="Update password",
 *     description="Update password",
 *     requestBody={"$ref": "#/components/requestBodies/MovePasswordAction payload"},
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *	   @OA\Response(
 *         response="401",
 *         description="Unathorized"
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Invalid values",
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 * 		 request="MovePasswordAction payload",
 *     description="password and old/new directory ids",
 *     required=true,
 *     @OA\Property(property="passwordId", type="integer", format="int64", description="Password id to move"),
 *     @OA\Property(property="originalFolder", type="integer", format="int64", description="Original folder"),
 *     @OA\Property(property="destinationFolder", type="integer", format="int64", description="Destination folder"),
 * )
 */
class MovePasswordAction implements RequestHandlerInterface
{
    /**
     *
     * @var PasswordFacade
     */
    protected $passwordFacade;

    /**
     *
     * @var ProblemDetailsresponseFactory
     */
    protected $problemDetailsFactory;

    /**
     *
     * @var FolderUserFacade
     */
    protected $folderUserFacade;

    /**
     *
     * @var userFacade
     */
    protected $userFacade;

    /**
     *
     * @var permissionFacade
     */
    protected $permissionFacade;

    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ProblemDetailsFactory
     * @param FolderUserFacade $foldeRUserFacade
     * @param UserFacade $userFacade
     */
    public function __construct(
        PasswordFacade $passwordFacade,
        ProblemDetailsResponseFactory $problemDetailsFactory,
        FolderUserFacade $folderUserFacade,
        UserFacade $userFacade,
        PermissionFacade $permissionFacade
    ) {
        $this->problemDetailsFactory = $problemDetailsFactory;
        $this->passwordFacade = $passwordFacade;
        $this->folderUserFacade = $folderUserFacade;
        $this->userFacade = $userFacade;
        $this->permissionFacade = $permissionFacade;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute("token", false)['sub'];
        $this->passwordFacade->setUserId($userId);

        $body = $request->getParsedBody();

        if (
            !isset($body["passwordId"]) ||
            !isset($body["originalFolder"]) ||
            !isset($body["destinationFolder"])
        ) {
            throw new ProblemDetailsException(
                400,
                'Missing body values',
                'Bad Request'
            );
        }
        $folderAccess = $this->folderUserFacade->checkUser(
            $body["destinationFolder"],
            $this->userFacade->get($userId)
        );

        $permission = $this->permissionFacade->getUserPermission($userId);

        if ($folderAccess == 2 || $permission->getAccessAllFolders()) {
            $password = $this->passwordFacade->movePassword(
                $body["passwordId"],
                $body["destinationFolder"]
            );
            return new JsonResponse(json_encode($password), 200);
        } else {
            throw new ProblemDetailsException(
                401,
                'Unauthorized',
                'Bad Request'
            );
        }
    }
}
