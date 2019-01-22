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
use Folder\Api\V1\Entity\Folder;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Permissions\Rbac\RoleInterface;

class FolderAssertion implements AssertionInterface
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

    /**
     * {@inheritDoc}
     */
    public function setRequest(
        \Psr\Http\Message\ServerRequestInterface $request
    ) {
        $this->request = $request;
    }

    /**
     * Set user on this class
     *
     * @param type $request
     * @return type
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * Returns folderId from Request - check Attributes and Body
     *
     * @param type $request
     * @return type
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

    public function assert(
        \Zend\Permissions\Rbac\Rbac $rbac,
        RoleInterface $role,
        string $permission
    ): bool {
        $folderId = $this->getFolderId($this->request);
        if ($folderId) {
            try {
                $access = $this->folderUserFacade->checkUser(
                    $folderId,
                    $this->user
                );
            } catch (ProblemDetailsException $ex) {
                // if we catch a 404 NOT FOUND exception, we need to return 401 instead (since this is an assertion)
                $userId = $this->user->getUserId();
                $method = $this->request->getMethod();

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

            if ($access == null) {
                return false;
            }
            return true;
        } else {
            return false;
        }
        return false;
    }
}
