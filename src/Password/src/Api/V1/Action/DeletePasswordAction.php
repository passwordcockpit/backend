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
use Password\Api\V1\Facade\PasswordFacade;
use Zend\Expressive\Hal\ResourceGenerator;

/**
 * @SWG\Delete(
 *     path="/v1/passwords/{passwordId}",
 *     summary="Deletes a password",
 *     description="",
 *     operationId="deletePassword",
 *     produces={"application/json"},
 *     tags={"passwords"},
 *     @SWG\Parameter(
 *         description="Password id to delete",
 *         in="path",
 *         name="passwordId",
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
 *         description="Dossier not found"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class DeletePasswordAction implements RequestHandlerInterface
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
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     */
    public function __construct(
        PasswordFacade $passwordFacade,
        ResourceGenerator $halResourceGenerator
    ) {
        $this->halResourceGenerator = $halResourceGenerator;
        $this->passwordFacade = $passwordFacade;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('Authentication\User');
        $this->passwordFacade->deletePassword(
            $request->getAttribute('id'),
            $user->getUserId()
        );
        return new \Zend\Diactoros\Response\EmptyResponse();
    }
}
