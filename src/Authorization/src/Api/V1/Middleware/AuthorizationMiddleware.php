<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Middleware;

use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Expressive\Router\RouteResult;
use Zend\Permissions\Rbac\Rbac;
use App\Service\ProblemDetailsException;
use Folder\Api\V1\Facade\FolderUserFacade;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Password\Api\V1\Entity\Password;
use File\Api\V1\Entity\File;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Facade\PermissionFacade;
use Password\Api\V1\Facade\PasswordFacade;
use Authorization\Api\V1\AssertionPluginManager;

class AuthorizationMiddleware implements MiddlewareInterface
{
    /**
     *
     * @var Rbac
     */
    private $rbac;

    /**
     *
     * @var FolderUserFacade
     */
    private $folderUserFacade;

    /**
     *
     * @var EntityManager
     */
    private $entityManager;

    /**
     *
     * @var Translator
     */
    private $translator;

    /**
     *
     * @var UserFacade
     */
    private $userFacade;

    /**
     *
     * @var PermissionFacade
     */
    private $permissionFacade;

    /**
     *
     * @var PasswordFacade
     */
    private $passwordFacade;

    /**
     *
     * @var AssertionPluginManager
     */
    private $assertionPluginManager;

    public function __construct(
        Rbac $rbac,
        FolderUserFacade $folderUserFacade,
        Translator $translator,
        EntityManager $entityManager,
        UserFacade $userFacade,
        PasswordFacade $passwordFacade,
        PermissionFacade $permissionFacade,
        AssertionPluginManager $assertionPluginManager
    ) {
        $this->rbac = $rbac;
        $this->folderUserFacade = $folderUserFacade;
        $this->translator = $translator;
        $this->entityManager = $entityManager;
        $this->userFacade = $userFacade;
        $this->passwordFacade = $passwordFacade;
        $this->permissionFacade = $permissionFacade;
        $this->assertionPluginManager = $assertionPluginManager;
    }

    /**
     * Returns folderId from Request - check Attributes and Body
     *
     * @param Request $request
     * @return int
     */
    private function getFolderId($request)
    {
        if ($request->getAttribute('folderId')) {
            $folderId = $request->getAttribute('folderId');
        } elseif ($request->getAttribute('id')) {
            $folderId = $request->getAttribute('id');
        } elseif (isset($request->getParsedBody()['parent_id'])) {
            $folderId = $request->getParsedBody()['parent_id'];
        } else {
            $folderId = null;
        }
        return $folderId;
    }

    /**
     * Returns passwordId from Request - check Attributes and Body
     *
     * @param type $request
     * @return type
     */
    private function getPasswordId($request)
    {
        if ($request->getAttribute('passwordId')) {
            $passwordId = $request->getAttribute('passwordId');
        } else {
            $passwordId = $request->getAttribute('id');
        }
        return $passwordId;
    }

    /**
     * Returns userId from Request attributes
     *
     * @param type $request
     * @return type
     */
    private function getUserId($request)
    {
        if ($request->getAttribute('userId')) {
            $userId = $request->getAttribute('userId');
        } else {
            $userId = $request->getAttribute('id');
        }
        return $userId;
    }

    private function checkRole($role)
    {
        if (!$this->rbac->hasRole($role)) {
            throw new ProblemDetailsException(
                401,
                sprintf(
                    $this->translator->translate("Role %s cannot be found"),
                    $role
                ),
                "Unauthorized",
                "https://httpstatuses.com/401"
            );
        }
    }

    /**
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ProblemDetailsException
     */
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        //check if token exist
        $token = $request->getAttribute("token", false);
        if (!$token) {
            return $handler->handle($request);
        }

        // check if route and matchedRoute exist
        $route = $request->getAttribute(RouteResult::class);
        if (!$route) {
            return $handler->handle($request);
        }
        if (!$route->getMatchedRoute()) {
            return $handler->handle($request);
        }

        $routeName = $route->getMatchedRoute()->getName();
        $path = $route->getMatchedRoute()->getPath();
        $method = $request->getMethod();

        // get user making the request
        $user = $request->getAttribute('Authentication\User');
        $userId = $user->getUserId();

        // get the permission of the user
        $roles = $this->permissionFacade->getUserPermissionArray($user);

        // values needed for the isGranted() function
        $assertion = null;
        $access = null;

        //loop on the permission
        foreach ($roles['roles'] as $role) {
            $assertion = null;
            $access = null;

            // check if role exists in rbac configuration
            $this->checkRole($role);

            // user role
            if ($role == 'user' || $role == 'view_logs') {
                // check if is granted
                if (
                    $this->rbac->isGranted($role, $routeName) &&
                    $this->assertionPluginManager->assert(
                        $this->rbac,
                        $role,
                        $routeName,
                        $request,
                        $user
                    )
                ) {
                    return $handler->handle($request);
                }
            } else {
                if ($this->rbac->isGranted($role, $routeName)) {
                    return $handler->handle($request);
                }
            }
        }
        throw new ProblemDetailsException(
            401,
            sprintf(
                $this->translator->translate(
                    "User %s cannot %s on this resource"
                ),
                $user->getUsername(),
                $method
            ),
            "Unauthorized",
            "https://httpstatuses.com/401"
        );
    }
}
