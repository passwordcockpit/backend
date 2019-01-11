<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Password\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Password\Api\V1\Entity\Password;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\ProblemDetails\ProblemDetailsResponseFactory;

/**
 *
 * @SWG\Put(
 *     path="/v1/passwords/{passwordId}",
 *     tags={"passwords"},
 *     operationId="updatePassword",
 *     summary="Update password",
 *     description="Update password",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *	   @SWG\Parameter(
 *         description="Password id to update",
 *         in="path",
 *         name="passwordId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 * 	   @SWG\Parameter(
 *        name="title",
 *        in="body",
 *        description="Password's title",
 *		  default="Title",
 * 		  type="string"
 *     ),
 *	   @SWG\Parameter(
 *        name="icon",
 *        in="body",
 *        description="Password's icon",
 *		  default="icon",
 * 		  type="string"
 *     ),
 *	   @SWG\Parameter(
 *        name="description",
 *        in="body",
 *        description="Password's description",
 *		  default="Description",
 * 		  type="string"
 *     ),
 * 	   @SWG\Parameter(
 *        name="username",
 *        in="body",
 *        description="Password's username",
 *		  default="username",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="password",
 *        in="body",
 *        description="Password's password",
 *		  default="password",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="url",
 *        in="body",
 *        description="Password's url",
 *		  default="http://www.ti.ch",
 * 		  type="string"
 *     ),
 *     @SWG\Parameter(
 *        name="tags",
 *        in="body",
 *        description="Password's tags",
 *		  default="tag1 tag2",
 * 		  type="string"
 *     ),
 *	   @SWG\Parameter(
 *        name="folder_id",
 *        in="body",
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
class UpdatePasswordAction implements RequestHandlerInterface
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
     * @var ProblemDetailsresponseFactory
     */
    protected $problemDetailsFactory;

    /**
     *
     * @var array
     */
    private $config;

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
     */
    public function __construct(
        PasswordFacade $passwordFacade,
        ProblemDetailsResponseFactory $problemDetailsFactory,
        $config,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->halResourceGenerator = $halResourceGenerator;
        $this->problemDetailsFactory = $problemDetailsFactory;
        $this->config = $config;
        $this->passwordFacade = $passwordFacade;
        $this->halResponseFactory = $halResponseFactory;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        // checking ldap info
        $token = $request->getAttribute("token", false);
        $authType = $token->data->ldap;

        if ($authType) {
            $response = $this->problemDetailsFactory->createResponse(
                $request,
                400,
                'Ldap is active, it is not possible to change password'
            );
            return $response;
        }

        $this->passwordFacade->setUserId(
            $request->getAttribute("token", false)->sub
        );
        $passwordId = $request->getAttribute('id');
        $password = $this->passwordFacade->update($passwordId, $request);
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
