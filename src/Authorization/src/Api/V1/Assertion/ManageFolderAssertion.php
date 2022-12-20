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
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Laminas\Permissions\Rbac\RoleInterface;
use User\Api\V1\Entity\User;

class ManageFolderAssertion implements AssertionInterface
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

    /**
     * {@inheritDoc}
     */
    public function setRequest(ServerRequestInterface $request) {
        $this->request = $request;
    }

    /**
     * Set user on this class
     *
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * Returns parentId from Request - check Attributes and Body
     *
     * @param ServerRequestInterface $request
     * @return mixed
     */
    private function getParentId($request)
    {
        return $request->getParsedBody()['parent_id'] ?? null;
    }

    /**
     * Returns folderId from Request - check Attributes and Body
     *
     * @param ServerRequestInterface $request
     * @return mixed
     */
    private function getFolderId(ServerRequestInterface $request)
    {
        if ($request->getAttribute('folderId')) {
            $folderId = $request->getAttribute('folderId');
        } elseif ($request->getAttribute('id')) {
            $folderId = $request->getAttribute('id');
        } elseif (isset($request->getParsedBody()['parent_id'])) {
            $folderId = $request->getParsedBody()['parent_id'];
        } elseif (isset($request->getParsedBody()['folder_id'])) {
            $folderId = $request->getParsedBody()['folder_id'];
        } else {
            $folderId = null;
        }
        return $folderId;
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
        if ($permission == 'api.v1.folders.create') {
            $folderId = $this->getParentId($this->request);
        } elseif ($permission == 'api.v1.passwords.move') {
            $folderId = $this->request->getParsedBody()['originalFolder'];
        } else {
            $folderId = $this->getFolderId($this->request);
        }
        if ($folderId) {
            $access = $this->folderUserFacade->checkUser(
                $folderId,
                $this->user
            );
            //access is now 'read' or 'manage' or null
            // check if it's 'manage'
            if ($access == null || $access == 1) {
                return false;
            }
            //user got 'manage' on folder
            return true;
        } elseif ($permission != 'api.v1.folders.list') {
            return false;
        } else {
            return false;
        }
        return false;
    }
}
