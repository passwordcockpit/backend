<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Assertion;

use Doctrine\ORM\EntityManager;
use Laminas\Permissions\Rbac\AssertionInterface;
use User\Api\V1\Facade\PermissionFacade;
use Folder\Api\V1\Facade\FolderUserFacade;
use Password\Api\V1\Facade\PasswordFacade;
use Log\Api\V1\Facade\LogFacade;
use Laminas\Permissions\Rbac\RoleInterface;
use Psr\Http\Message\ServerRequestInterface;
use Password\Api\V1\Entity\Password;
use Laminas\I18n\Translator\Translator;
use User\Api\V1\Entity\User;

class LogsAssertion implements AssertionInterface
{
    protected ServerRequestInterface $request;
    protected User $user;

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param FolderUserFacade $folderUserFacade
     * @param PermissionFacade $permissionFacade
     * @param PasswordFacade $passwordFacade
     * @param LogFacade $logFacade
     * @param EntityManager $entityManager
     */
    public function __construct(
        protected Translator $translator,
        protected FolderUserFacade $folderUserFacade,
        protected PermissionFacade $permissionFacade,
        protected PasswordFacade $passwordFacade,
        protected LogFacade $logFacade,
        protected EntityManager $entityManager
    ){}

    /**
     * {@inheritDoc}
     */
    public function setRequest(ServerRequestInterface $request) {
        $this->request = $request;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Returns passwordId from Request - check Attributes and Body
     *
     * @param ServerRequestInterface $request
     * @return mixed
     */
    private function getPasswordId(ServerRequestInterface $request)
    {
        $passwordId = null;
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
     * @param ServerRequestInterface $request
     * @return mixed
     */
    private function getUserId(ServerRequestInterface $request)
    {
        if ($request->getAttribute('userId')) {
            $userId = $request->getAttribute('userId');
        } else {
            $userId = $request->getAttribute('id');
        }
        return $userId;
    }

    /**
     * Returns logId from Request attributes
     *
     * @param ServerRequestInterface $request
     * @return mixed
     */
    private function getLogId(ServerRequestInterface $request)
    {
        return $request->getAttribute('id');
    }

    private function checkLogId()
    {
        $logId = $this->getLogId($this->request);
        $log = $this->logFacade->getLog($logId);
        if (isset($log)) {
            $userIdLog = $log->getUser()->getUserId();
            $userIdrequest = $this->user->getUserId();

            // if the user making the request is the same user specified in the log
            if ($userIdLog == $userIdrequest) {
                return true;
            }

            // we can reuse the function below to check if the password
            // that is linked to the log is available to the user making the request
            $passwordId = $log->getPassword()->getPasswordId();
            if ($this->checkPasswordId($passwordId)) {
                return true;
            }
        }
        // user is not related to the log
        return false;
    }

    private function checkPasswordId($passwordId)
    {
        if ($passwordId) {
            $password = $this->entityManager
                ->getRepository(Password::class)
                ->find($passwordId);
            if ($password) {
                // getting folderId of password
                $folderId = $password->getFolder()->getFolderId();
                //need to check now that user has 'read' or 'manage' on folder.

                $access = $this->folderUserFacade->checkUser(
                    $folderId,
                    $this->user
                );

                if ($access == 1 || $access == 2) {
                    return true;
                }
            } else {
                // password not found
                return false;
            }
        }
        return false;
    }

    private function checkUserId()
    {
        $userIdRequest = $this->getUserId($this->request);
        $userId = $this->user->getUserId();
        if ($userIdRequest == $userId) {
            return true;
        }
        return false;
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
        // need to check if the user has manage_user or access_all_folders  --> return true
        $roles = $this->permissionFacade->getUserPermissionArray($this->user);

        if (
            in_array('manage_users', $roles['roles']) ||
            in_array('access_all_folders', $roles['roles'])
        ) {
            return true;
        }
        // else need to check if password id or user id are compatible with the user making the request (e.g. same ID). --> true/false

        $allowUser = false;

        switch ($permission) {
            case 'api.v1.logs.get':
                $allowUser = $this->checkLogId();
                break;
            case 'api.v1.users.logs.list':
                $allowUser = $this->checkUserId();
                break;
            case 'api.v1.passwords.logs.list':
                $passwordId = $this->getPasswordId($this->request);
                $allowUser = $this->checkPasswordId($passwordId);
                break;
        }

        return $allowUser;
    }
}
