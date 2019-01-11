<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Assertion;

use Zend\Permissions\Rbac\AssertionInterface;
use Psr\Http\Message\ServerRequestInterface;
use User\Api\V1\Entity\User;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Zend\Mvc\I18n\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Permissions\Rbac\RoleInterface;

class UserAssertion implements AssertionInterface
{
    protected $translator;
    protected $folderUserFacade;
    protected $request;
    protected $user;

    public function __construct(
        Translator $translator,
        FolderUserFacade $folderUserFacade
    ) {
        $this->translator = $translator;
        $this->folderUserFacade = $folderUserFacade;
    }

    /**
     * {@inheritDoc}
     */
    public function setRequest(
        \Psr\Http\Message\ServerRequestInterface $request
    ) {
        $this->request = $request;
    }

    public function setUser($user)
    {
        $this->user = $user;
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

    public function assert(
        \Zend\Permissions\Rbac\Rbac $rbac,
        RoleInterface $role,
        string $permission
    ): bool {
        $userIdRequest = $this->getUserId($this->request);
        $userId = $this->user->getUserId();
        if ($userIdRequest == $userId) {
            return true;
        }
        return false;
    }
}
