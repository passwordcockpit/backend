<?php

/**
 * GetUserAction
 *
 * @package User\Api\V1\Action
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use App\Service\ProblemDetailsException;
use Zend\Expressive\Hal\HalResponseFactory;
use User\Api\V1\Facade\UserFacade;
use Zend\Expressive\Hal\ResourceGenerator;

/**
 * @SWG\Get(
 *     path="/v1/users/{userId}",
 *     summary="Get a user",
 *     description="Returns a user by its id",
 *     operationId="getUser",
 *     produces={"application/json"},
 *     tags={"users"},
 *     @SWG\Parameter(
 *         description="User id to fetch",
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
 *         response=404,
 *         description="Not Found"
 *     ),
 * security={{"bearerAuth": {}}}
 * )
 */
class GetUserAction implements RequestHandlerInterface
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

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ProblemDetailsException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user_id = $request->getAttribute('id'); // recupero l'id dalla Route
        $user = $this->userFacade->get($user_id); // recupero lo user dalla user facade (che usa Doctrine per recuperare lo user dal DB)
        if ($user) {
            // ho trovato lo user
            $resource = $this->halResourceGenerator->fromObject(
                $user,
                $request
            );
            return $this->halResponseFactory->createResponse(
                $request,
                $resource
            );
        } else {
            throw new ProblemDetailsException(404, "Not Found", "", "", []);
        }
    }
}
