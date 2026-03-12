<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Password\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use User\Api\V1\Facade\PermissionFacade;
use Mezzio\Hal\HalResponseFactory;
use Mezzio\Hal\ResourceGenerator;
use Password\Api\V1\Collection\PasswordCollection;

/**
 * @OA\Get(
 *     path="/v1/passwords",
 *     tags={"passwords"},
 *     operationId="listPasswords",
 *     summary="Get a list of password",
 *     description="Get a list of passwords. Use ?q=... to search for specific passwords.",
 *     @OA\Response(
 *         response=200,
 *         description="Ok",
 *         @OA\JsonContent()
 *     ),
 *     security={{"bearerAuth": {}}}
 * )
 */
class ListPasswordAction implements RequestHandlerInterface
{
    /**
     * Constructor
     *
     * @param PasswordFacade $passwordFacade
     * @param PermissionFacade $permissionFacade
     * @param ResourceGenerator $halResourceGenerator
     * @param HalResponseFactory $halResponseFactory
     */
    public function __construct(
        protected PasswordFacade $passwordFacade,
        protected PermissionFacade $permissionFacade,
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
        $queryParams = $request->getQueryParams();

        $queryParam = "";

        // Checks if there are params
        if (sizeof($queryParams) > 0 && isset($queryParams['q'])) {
            $queryParam = $queryParams['q'];
        }

        $user = $request->getAttribute('Authentication\User');

        // Get user permissions
        $perms = $this->permissionFacade->getUserPermission($user->getUserId());

        // Check 'access_all_folder'
        if ($perms->getAccessAllFolders()) {
            // Get passwords, then setting the correct file to it
            $passwords = $this->passwordFacade->getAllPasswordsbySearch(
                $queryParam
            );
            $passwords = $this->passwordFacade->associatePasswordsFiles(
                $passwords
            );
        } else {
            // Just a user without permissions

            // Get passwords related to the user, then setting the correct file to it
            $passwords = $this->passwordFacade->getPasswordsbySearch(
                $queryParam,
                $user->getUserId()
            );
            $passwords = $this->passwordFacade->associatePasswordsFiles(
                $passwords
            );
        }

        // Decrypt passwords
        foreach ($passwords as $pass) {
            $decryptedPass = $this->passwordFacade->decrypt(
                $pass->getPassword()
            );
            $pass->setPassword($decryptedPass);
        }

        // Transform passwords to collection, so we can create a HalResource
        $passwordsArrayAdapter = new \Laminas\Paginator\Adapter\ArrayAdapter(
            $passwords
        );
        $passwordsCollection = new PasswordCollection($passwordsArrayAdapter);
        $passwordsCollection->setDefaultItemCountPerPage(PHP_INT_MAX);

        $resource = $this->halResourceGenerator->fromObject(
            $passwordsCollection,
            $request
        );

        return $this->halResponseFactory->createResponse($request, $resource);
    }
}
