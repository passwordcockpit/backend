<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Mezzio\Hal\HalResponseFactory;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGenerator;
use User\Api\V1\Collection\UserCollection;

/**
 * @OA\Get(
 *     path="/v1/users",
 *     summary="List users",
 *     description="Returns the complete list of users",
 *     operationId="listUsers",
 *     tags={"users"},
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
class ListUserAction implements RequestHandlerInterface
{
    /**
     *
     * @var UserFacade
     */
    protected $userFacade;

    /**
     *
     * @var ResourceGenerator
     */
    private $halResourceGenerator;

    /**
     *
     * @var HalResponseFactory
     */
    protected $halResponseFactory;

    /**
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        UserFacade $userFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->userFacade = $userFacade;
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
        $users = $this->userFacade->getAll(); // ottengo la lista users dalla userFacade
        $usersArrayAdapter = new \Laminas\Paginator\Adapter\ArrayAdapter($users);
        $usersCollection = new UserCollection($usersArrayAdapter);
        $usersCollection->setDefaultItemCountPerPage(PHP_INT_MAX); // setto al Paginator la dimensione uguale al MAX INT di PHP
        $resource = $this->halResourceGenerator->fromObject(
            $usersCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
