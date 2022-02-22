<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Assertion;

use Laminas\Permissions\Rbac\AssertionInterface;
use Psr\Http\Message\ServerRequestInterface;
use Password\Api\V1\Entity\Password;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Laminas\Permissions\Rbac\RoleInterface;
use User\Api\V1\Entity\User;

class PasswordAssertion implements AssertionInterface
{
    protected ServerRequestInterface $request;

    protected User $user;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     * @param FolderUserFacade $folderUserFacade
     */
    public function __construct(
        protected EntityManager $entityManager,
        protected Translator $translator,
        protected FolderUserFacade $folderUserFacade
    ){}

    public function setRequest(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns passwordId from Request - check Attributes and Body
     *
     * @param ServerRequestInterface $request
     * @return mixed
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
     * Function that gets called by the interface
     *
     * @param Rbac $rbac
     * @param RoleInterface $role
     * @param string $permission
     *
     * @return bool
     */
    public function assert(
        \Laminas\Permissions\Rbac\Rbac $rbac,
        RoleInterface $role,
        string $permission
    ): bool {
        $passwordId = $this->getPasswordId($this->request);

        $userId = $this->user->getUserId();
        $method = $this->request->getMethod();

        if ($passwordId) {
            $password = $this->entityManager
                ->getRepository(Password::class)
                ->find($passwordId);
            if ($password) {
                $folderId = $password->getFolder()->getFolderId();
            } else {
                throw new ProblemDetailsException(
                    401,
                    sprintf(
                        $this->translator->translate(
                            "User %s cannot %s on this resource"
                        ),
                        $this->user->getUsername(),
                        $method
                    ),
                    "Unauthorized",
                    "https://httpstatuses.com/401"
                );
            }
        } else {
            return false;
        }
        $access = $this->folderUserFacade->checkUser($folderId, $this->user);
        if ($access == null) {
            return false;
        }
        return true;
    }
}
