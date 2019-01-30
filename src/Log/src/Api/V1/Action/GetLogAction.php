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
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Log\Api\V1\Facade\LogFacade;

/**
 * @SWG\Get(
 *     path="/v1/logs/{logId}",
 *     summary="Get log",
 *     description="Returns a log by its id",
 *     operationId="getLog",
 *     produces={"application/json"},
 *     tags={"logs"},
 *     @SWG\Parameter(
 *         description="Log id to fetch",
 *         in="path",
 *         name="logId",
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
class GetLogAction implements RequestHandlerInterface
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
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        LogFacade $logFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->logFacade = $logFacade;
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
        $logId = $request->getAttribute('id');
        $log = $this->logFacade->getLog($logId);
        if ($log) {
            $resource = $this->halResourceGenerator->fromObject($log, $request);
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        }
        return new \Zend\Diactoros\Response\EmptyResponse();
    }
}
