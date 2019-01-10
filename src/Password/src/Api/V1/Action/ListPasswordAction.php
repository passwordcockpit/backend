<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Password\Api\V1\Action;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Password\Api\V1\Facade\PasswordFacade;
use User\Api\V1\Facade\PermissionFacade;
use Zend\Expressive\Hal\HalResponseFactory;
use Zend\Expressive\Hal\ResourceGenerator;
use Password\Api\V1\Collection\PasswordCollection;
use Zend\Expressive\Hal\HalResource;

class ListPasswordAction implements RequestHandlerInterface
{
    /**
     *
     * @var PasswordFacade
     */
    protected $passwordFacade;

    /**
     *
     * @var PermissionFacade
     */
    protected $permissionFacade;

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
     */
    public function __construct(
        PasswordFacade $passwordFacade,
        PermissionFacade $permissionFacade,
        ResourceGenerator $halResourceGenerator,
        HalResponseFactory $halResponseFactory
    ) {
        $this->passwordFacade = $passwordFacade;
        $this->permissionFacade = $permissionFacade;
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
        $queryParams = $request->getQueryParams();

        $queryParam = "";

        // checks if there are params
        if (sizeof($queryParams) > 0 && isset($queryParams['q'])) {
            $queryParam = $queryParams['q'];
        }

        $user = $request->getAttribute('Authentication\User');

        // getting user permissions
        $perms = $this->permissionFacade->getUserPermission($user->getUserId());

        // have 'access_all_folder'
        if ($perms->getAccessAllFolders()) {
            // getting passwords, then setting the correct file to it
            $passwords = $this->passwordFacade->getAllPasswordsbySearch(
                $queryParam
            );
            $passwords = $this->passwordFacade->associatePasswordsFiles(
                $passwords
            );
        } else {
            //just a user without permissions

            //getting passwords related to the user, then setting the correct file to it
            $passwords = $this->passwordFacade->getPasswordsbySearch(
                $queryParam,
                $user->getUserId()
            );
            $passwords = $this->passwordFacade->associatePasswordsFiles(
                $passwords
            );
        }

        // transforming passwords to collection,
        // so we can create a HalResource
        $passwordsArrayAdapter = new \Zend\Paginator\Adapter\ArrayAdapter(
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
