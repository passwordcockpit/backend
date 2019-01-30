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
use File\Api\V1\Entity\File;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\Translator;
use Folder\Api\V1\Facade\FolderUserFacade;
use Zend\Permissions\Rbac\RoleInterface;

class FileManageAssertion implements AssertionInterface
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

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     * @param FolderUserFacade $folderUserFacade
     */
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
        $fileId = $this->request->getAttribute('id');
        if ($fileId) {
            $file = $this->entityManager
                ->getRepository(File::class)
                ->find($fileId);
            if ($file) {
                $password = $file->getPassword();
                if ($password) {
                    $folderId = $password->getFolder()->getFolderId();
                }
                // Password not found
                else {
                    throw new ProblemDetailsException(
                        404,
                        $this->translator->translate('Password not found'),
                        $this->translator->translate('Resource not found'),
                        'https://httpstatus.es/404'
                    );
                }
            }
            // File not found
            else {
                throw new ProblemDetailsException(
                    404,
                    $this->translator->translate('File not found'),
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
