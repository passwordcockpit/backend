<?php

/**
 * GetPasswordAction
 *
 * @package User\Api\V1\Action
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use File\Api\V1\Facade\FileFacade;
use Mezzio\Hal\ResourceGenerator;
use Mezzio\Hal\HalResponseFactory;

/**
 * @OA\Get(
 *     path="/v1/passwords/{passwordId}",
 *     tags={"passwords"},
 *     operationId="getPassword",
 *     summary="Get a password",
 *     description="",
 *     @OA\Parameter(
 *         description="Password id to fetch",
 *         in="path",
 *         name="passwordId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Not found",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class GetPasswordAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param FileFacade $fileFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected PasswordFacade $passwordFacade,
        protected FileFacade $fileFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory
    ){}

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        // set the User for logging purpose
        $this->passwordFacade->setUserId(
            $request->getAttribute("token", false)['sub']
        );
        $password = $this->passwordFacade->get($request->getAttribute('id'));
        $file = $this->fileFacade->getFiles($password->getPasswordId());

        $resource = $this->halResourceGenerator->fromObject(
            $password,
            $request
        );

        // file is currently an array, but a password should only have one file associated so let's just return $file[0].
        if (isset($file[0])) {
            $fileId = $file[0]->getFileId();
            $fileName = $file[0]->getName();

            $resource = $resource->withElement("fileId", $fileId);
            $resource = $resource->withElement("fileName", $fileName);
        }
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
