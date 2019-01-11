<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Zend\Expressive\Hal\ResourceGenerator;
use Zend\Expressive\Hal\HalResponseFactory;
use File\Api\V1\Facade\FileFacade;

/**
 *
 * @SWG\Get(
 *     path="/v1/files/{fileId}",
 *     tags={"File"},
 *     operationId="GetFileAction",
 *     summary="Get file",
 *     description="",
 *     consumes={"application/json"},
 *     produces={"application/json"},
 *     @SWG\Parameter(
 *         name="fileId",
 *         in="path",
 *         description="File id",
 *         required=true,
 * 		   type="string"
 *     ),
 *     @SWG\Response(
 *         response=200,
 *         description="Ok"
 *     ),
 *     security={
 *       {"bearerAuth": {}}
 *     }
 * )
 *
 */
class GetFileAction implements RequestHandlerInterface
{
    /**
     * @var ResourceGenerator
     */
    private $resourceGenerator;

    /**
     * @var HalResponseFactory
     */
    private $halResponseFactory;

    /**
     *
     * @var FileFacade
     */
    private $fileFacade;

    /**
     *
     * @param ResourceGenerator $resourceGeneratorInstance
     * @param HalResponseFactory $halResponseFactory
     * @param FileFacade $fileFacade
     */
    public function __construct(
        ResourceGenerator $resourceGeneratorInstance,
        HalResponseFactory $halResponseFactory,
        FileFacade $fileFacade
    ) {
        $this->resourceGenerator = $resourceGeneratorInstance;
        $this->halResponseFactory = $halResponseFactory;
        $this->fileFacade = $fileFacade;
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request) : ResponseInterface
    {
        $file = $this->fileFacade->fetch($request->getAttribute('id'));
        $resource = $this->resourceGenerator->fromObject($file, $request);
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
