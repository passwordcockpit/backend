<?php

/**
 * UpdateUserLanguagAction
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Christian Willemse <christian.willemse@blackpoints.ch>
 */

namespace User\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use User\Api\V1\Facade\UserFacade;
use Mezzio\Hal\ResourceGenerator;
use User\Api\V1\Entity\User;
use Mezzio\Hal\HalResponseFactory;
use Firebase\JWT\JWT;
use Authentication\Api\V1\Facade\TokenUserFacade;
use Firebase\JWT\Key;

/**
 *
 * @copyright 2018 Blackpoints SA
 *
 * @OA\Patch(
 *     path="/v1/users/{userId}/language",
 *     tags={"users"},
 *     operationId="updateUserLanguage",
 *     summary="Update user language",
 *     description="Update the language of the user",
 *     @OA\Parameter(
 *         description="User id to update",
 *         in="path",
 *         name="userId",
 *         required=true,
 *         @OA\Schema(
 *             type="integer",
 *             format="int64"
 *         )
 *     ),
 *     requestBody={"$ref": "#/components/requestBodies/UpdateUserLanguageAction payload"},
 *     @OA\Response(
 *         response=200,
 *         description="OK",
 *         @OA\JsonContent()
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="User not found"
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 * @OA\RequestBody(
 *		request="UpdateUserLanguageAction payload",
 *    description="User object that needs to be updated",
 *    required=true,
 *		@OA\Property(property="language", type="string", description="User's language")
 * )
 */
class UpdateUserLanguageAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     * @param array $config
     * @param TokenUserFacade $tokenUserFacade
     */
    public function __construct(
        protected UserFacade $userFacade,
        protected ResourceGenerator $halResourceGenerator,
        protected HalResponseFactory $halResponseFactory,
        private array $config,
        private readonly TokenUserFacade $tokenUserFacade
    ){}

    /**
     * Return a new token with the language changed.
     * if language is changed, return updated token
     * if language is not changed, return normal token
     * 
     * @param ServerRequestInterface $request
     * @return string
     * 
     */
    private function updateTokenSpecifics($request)
    {
        $token1 = $request->getHeader("Authorization")[0];
        $token = substr($token1, 7);
        $tokenUser = $this->tokenUserFacade->getByToken($token)[0];

        $payLoad = JWT::decode($token, new Key($this->config['secret_key'], 'HS256'));
        $specifics = $request->getParsedBody();

        foreach ($specifics as $spec => $value) {
            if ($spec === 'language' && $value != null) {
                $payLoad->data->$spec = $value;
                $token = JWT::encode(
                    $payLoad,
                    $this->config['secret_key'],
                    "HS256"
                );
            }
        }

        //update token on tokenUser table
        $this->tokenUserFacade->updateTokenUser($tokenUser, $token, false);

        return $token;
    }

    /**
     * MiddlewareInterface handler
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $user = $request->getAttribute('Authentication\User');
        $entityManager = $this->userFacade->getEntityManager();
        $payload = $request->getParsedBody();

        // update user language
        $user->setLanguage($payload['language']);
        $entityManager->persist($user);
        $entityManager->flush();

        // update token
        $token = $this->updateTokenSpecifics($request);

        // generate resource
        $this->halResourceGenerator
            ->getMetadataMap()
            ->get(User::class)
            ->setRouteParams(['id' => $user->getUserId()]);

        $resource = $this->halResourceGenerator->fromObject($user, $request);
        $resource = $resource->withElement("token", $token);

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
