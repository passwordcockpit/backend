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
use Mezzio\Hal\ResourceGenerator;

/**
 * @OA\Delete(
 *     path="/v1/passwords/{passwordId}",
 *     summary="Deletes a password",
 *     description="",
 *     operationId="deletePassword",
 *     tags={"passwords"},
 *     @OA\Parameter(
 *         description="Password id to delete",
 *         in="path",
 *         name="passwordId",
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
 *         response=404,
 *         description="Dossier not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class DeletePasswordAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     */
    public function __construct(
        protected PasswordFacade $passwordFacade,
        protected ResourceGenerator $halResourceGenerator
    ){}

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
        return new \Laminas\Diactoros\Response\EmptyResponse();
    }
}
