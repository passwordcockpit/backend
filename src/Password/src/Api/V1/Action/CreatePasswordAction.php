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
use Zend\Expressive\Hal\HalResponseFactory;
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Password\Api\V1\Entity\Password;

/**
 *
 * @SWG\Post(
 *     path="/v1/passwords",
 *     tags={"passwords"},
 *     operationId="createPassword",
 *     summary="Create a new password",
 *     description="Create a new password",
 *     consumes={"multipart/form-data"},
 *     produces={"application/json"},
 * 	   @SWG\Parameter(
 * 		   name="file",
 *         description="file to upload",
 *         in="formData",
 *         type="file"
 *     ),
 * 	   @SWG\Parameter(
 *        name="title",
 *        in="formData",
 *        description="Password's title",
 *		  default="Title",
 * 		  type="string"
 *     ),
 *	   @SWG\Parameter(
 *        name="icon",
 *        in="formData",
 *        description="Password's icon",
 *		  default="icon",
 * 		  type="string"
 *     ),
 *	   @SWG\Parameter(
 *        name="description",
 *        in="formData",
 *        description="Password's description",
 *		  default="Description",
 * 		  type="string"
 *     ),
 * 	   @SWG\Parameter(
 *        name="username",
 *        in="formData",
 *        description="Password's username",
 *		  default="username",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="password",
 *        in="formData",
 *        description="Password's password",
 *		  default="password",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="url",
 *        in="formData",
 *        description="Password's url",
 *		  default="http://www.ti.ch",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="tags",
 *        in="formData",
 *        description="Password's tags",
 *		  default="tag1 tag2",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="folder_id",
 *        in="formData",
 *        description="Folder id where the password will be",
 * 		  type="integer"
 *     ),
 *     @SWG\Response(
 *         response=201,
 *         description="OK",
 *     ),
 *	   @SWG\Response(
 *         response="400",
 *         description="Mime type not allowed",
 *     ),
 *     @SWG\Response(
 *         response=404,
 *         description="Invalid input",
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class CreatePasswordAction implements RequestHandlerInterface
{
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
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        PasswordFacade $passwordFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
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
