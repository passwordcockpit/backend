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
use Log\Api\V1\Facade\LogFacade;
use Mezzio\Hal\ResourceGenerator;
use Log\Api\V1\Collection\UserLogCollection;

/**
 * @OA\Get(
 *     path="/v1/users/{userId}/logs",
 *     summary="List logs for specified user",
 *     description="Returns list of logs for specified user",
 *     operationId="listLogsUser",
 *     tags={"users"},
 *     @OA\Parameter(
 *         description="User id to fetch logs",
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
class ListUserLogAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param LogFacade $logFacade
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param array $paginatorCofig
     */
    public function __construct(
        protected LogFacade $logFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory,
        protected $paginatorConfig
    ){}

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $userId = $request->getAttribute('id');
        $logs = $this->logFacade->getUserLog($userId);
        //most recent logs are shown
        // $logs = array_reverse($logs);
        usort($logs, fn($a, $b) => $b->getActionDate()->getTimestamp() -
            $a->getActionDate()->getTimestamp());
        $logsArrayAdapter = new \Laminas\Paginator\Adapter\ArrayAdapter($logs);
        $logsCollection = new UserLogCollection($logsArrayAdapter);
        $logsCollection->setDefaultItemCountPerPage(
            $this->paginatorConfig['small']
        );
        $resource = $this->halResourceGenerator->fromObject(
            $logsCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
