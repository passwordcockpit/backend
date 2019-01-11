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
use Zend\Expressive\Hal\HalResponseFactory;
use User\Api\V1\Facade\UserFacade;
use Zend\Expressive\Hal\ResourceGenerator;
use User\Api\V1\Collection\UserCollection;

/**
 * @SWG\Get(
 *     path="/v1/users/usernames",
 *     summary="List users, but only id, username, surname, name, enabled status",
 *     description="Returns a public list of users",
 *     operationId="listUsernames",
 *     produces={"application/json"},
 *     tags={"users"},
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
class ListUsernameAction implements RequestHandlerInterface
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
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $users = $this->userFacade->getAllUsernames(); // ottengo la lista users dalla userFacade
        $usersArrayAdapter = new \Zend\Paginator\Adapter\ArrayAdapter($users);
        $usersCollection = new UserCollection($usersArrayAdapter);
        $usersCollection->setDefaultItemCountPerPage(PHP_INT_MAX); // setto al Paginator la dimensione uguale al MAX INT di PHP
        $resource = $this->halResourceGenerator->fromObject(
            $usersCollection,
            $request
        );
        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
