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
use Password\Api\V1\Entity\Password;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Permissions\Rbac\RoleInterface;

class ManagePasswordAssertion implements AssertionInterface
{
    /**
     *
     * @var EntityManager
     *
     */
    protected $entityManager;

    /**
     *
     * @var Translator
     *
     */
    protected $translator;

    /**
     *
     * @var FolderUserFacade
     *
     */
    protected $folderUserFacade;

    /**
     *
     * @var ServerRequestInterface
     *
     */
    protected $request;

    protected $user;

    public function __construct(
        EntityManager $entityManager,
        Translator $translator,
        FolderUserFacade $folderUserFacade
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->folderUserFacade = $folderUserFacade;
    }

    public function setRequest($request)
    {
        $this->request = $request;
    }

    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Returns passwordId from Request - check Attributes and Body
     *
     * @param type $request
     * @return type
     */
    private function getPasswordId($request)
    {
        $passwordId = $request->getAttribute('id');

        return $passwordId;
    }

    public function assert(
        \Zend\Permissions\Rbac\Rbac $rbac,
        RoleInterface $role,
        string $permission
    ): bool {
        $passwordId = $this->getPasswordId($this->request);
        if ($passwordId) {
            $password = $this->entityManager
                ->getRepository(Password::class)
                ->find($passwordId);
            if ($password) {
                $folderId = $password->getFolder()->getFolderId();
            } else {
                throw new ProblemDetailsException(
                    404,
                    $this->translator->translate('Password not found'),
                    $this->translator->translate('Resource not found'),
                    'https://httpstatus.es/404'
                );
            }
        } else {
            return false;
        }
        $access = $this->folderUserFacade->checkUser($folderId, $this->user);
        if ($access == null || $access == 1) {
            return false;
        }
        return true;
    }
}
