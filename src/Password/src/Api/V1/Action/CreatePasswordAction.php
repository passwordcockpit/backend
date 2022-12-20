<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Hal\HalResponseFactory;
use Password\Api\V1\Facade\PasswordFacade;
use Mezzio\Hal\ResourceGenerator;
use Password\Api\V1\Entity\Password;

/**
 *
 * @OA\Post(
 *     path="/v1/passwords",
 *     tags={"passwords"},
 *     operationId="createPassword",
 *     summary="Create a new password",
 *     description="Create a new password",
 *     requestBody={"$ref": "#/components/requestBodies/CreatePasswordAction payload"},
 *     @OA\Response(
 *         response=201,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *	   @OA\Response(
 *         response="400",
 *         description="Mime type not allowed",
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Invalid input",
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 *     request="CreatePasswordAction payload",
 *     description="password object to create",
 *     required=true,
 *     @OA\MediaType(mediaType="multipart/form-data"),
 *     @OA\Property(property="file", type="file", description="file to upload"),
 *     @OA\Property(property="title", type="string", default="Title", description="Password's title"),
 *     @OA\Property(property="icon", type="string", default="icon", description="Password's icon"),
 *     @OA\Property(property="description", type="string", default="Description", description="Password's description"),
 *     @OA\Property(property="username", type="string", default="username", description="Password's username"), 
 *     @OA\Property(property="password", type="string", default="password", description="Password's password"), 
 *     @OA\Property(property="url", type="string", default="http://www.ti.ch", description="Password's url"),
 *     @OA\Property(property="tags", type="string", default="tag1 tag2", description="Password's tags"), 
 *     @OA\Property(property="folder_id", type="string", description="Folder id where the password will be"), 
 * )
 */
class CreatePasswordAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected PasswordFacade $passwordFacade,
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
        $this->passwordFacade->setUserId(
            $request->getAttribute("token", false)['sub']
        );
        $password = $this->passwordFacade->createPassword($request);
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(Password::class)
            ->setRouteParams(['id' => $password->getPasswordId()]);
        $resource = $this->halResourceGenerator->fromObject(
            $password,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
