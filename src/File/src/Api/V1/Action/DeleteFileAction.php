<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Action;

use Psr\Http\Server\RequestHandlerInterface;
use File\Api\V1\Facade\FileFacade;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Mezzio\Hal\ResourceGenerator;

/**
 * @OA\Delete(
 *     path="/v1/files/{fileId}",
 *     summary="Deletes a file",
 *     description="",
 *     operationId="deleteFile",
 *     tags={"File"},
 *     @OA\Parameter(
 *         description="Fileid to delete",
 *         in="path",
 *         name="fileId",
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
 *         description="File not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */

class DeleteFileAction implements RequestHandlerInterface
{
    /**
     *
     * @var FileFacade
     */
    protected $fileFacade;

    /**
     *
     * @var ResourceGenerator
     */
    protected $halResourceGenerator;

    /**
     * Constructor
     *
     * @param File $fileFacade
     * @param ResourceGenerator $halResourceGenerator
     */
    public function __construct(
        FileFacade $fileFacade,
        ResourceGenerator $halResourceGenerator
    ) {
        $this->fileFacade = $fileFacade;
        $this->halResourceGenerator = $halResourceGenerator;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $result = $this->fileFacade->delete($request->getAttribute('id'));
        if ($result) {
            return new \Laminas\Diactoros\Response\EmptyResponse();
        }
    }
}
