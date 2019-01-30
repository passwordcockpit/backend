<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authorization\Api\V1\Assertion;

use Zend\Permissions\Rbac\AssertionInterface;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Permissions\Rbac\RoleInterface;
use User\Api\V1\Entity\User;

class UsernameListAssertion implements AssertionInterface
{
    protected $translator;
    protected $folderUserFacade;
    protected $request;
    protected $user;

    /**
     * Constructor
     *
     * @param Translator $translator
     * @param FolderUserFacade $folderUserFacade
     */
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
     * Function that gets called by the interface
     *
     * @param Rbac $rbac
     * @param RoleInterface $role
     * @param string $permission
     *
     * @return bool
     */
    public function assert(
        \Zend\Permissions\Rbac\Rbac $rbac,
        RoleInterface $role,
        string $permission
    ): bool {
        // return true only if user has a 'manage' somewhere
        return $this->folderUserFacade->checkUserManage($this->user);
    }
}
