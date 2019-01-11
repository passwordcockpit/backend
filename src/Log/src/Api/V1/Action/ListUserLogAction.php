<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Log\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Hal\HalResponseFactory;
use Log\Api\V1\Facade\LogFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use Log\Api\V1\Collection\UserLogCollection;

/**
 * @SWG\Get(
 *     path="/v1/users/{userId}/logs",
 *     summary="List logs for specified user",
 *     description="Returns list of logs for specified user",
 *     operationId="listLogsUser",
 *     produces={"application/json"},
 *     tags={"users"},
 *     @SWG\Parameter(
 *         description="User id to fetch logs",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         type="integer",
 *         format="int64"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="OK"
 *     ),
 *     @SWG\Response(
 *         response=204,
 *         description="No Content"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class ListUserLogAction implements RequestHandlerInterface
{
    /**
     *
     * @var LogFacade
     */
    protected $logFacade;

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
     *
     * @var array
     */
    protected $paginatorConfig;

    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     */
    public function __construct(
        LogFacade $logFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory,
        $paginatorConfig
    ) {
        $this->halResourceGenerator = $halResourceGenerator;
        $this->halResponseFactory = $halResponseFactory;
        $this->logFacade = $logFacade;
        $this->paginatorConfig = $paginatorConfig;
    }

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
        usort($logs, function ($a, $b) {
            return $b->getActionDate()->getTimestamp() -
                $a->getActionDate()->getTimestamp();
        });
        $logsArrayAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($logs);
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
