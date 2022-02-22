<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Log\Api\V1\Facade\LogFacade;

/**
 * @OA\Get(
 *     path="/v1/logs/{logId}",
 *     summary="Get log",
 *     description="Returns a log by its id",
 *     operationId="getLog",
 *     tags={"logs"},
 *     @OA\Parameter(
 *         description="Log id to fetch",
 *         in="path",
 *         name="logId",
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
class GetLogAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected LogFacade $logFacade,
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
        $logId = $request->getAttribute('id');
        $log = $this->logFacade->getLog($logId);
        if ($log) {
            $resource = $this->halResourceGenerator->fromObject($log, $request);
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        }
        return new \Laminas\Diactoros\Response\EmptyResponse();
    }
}
