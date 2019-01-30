<?php

/**
 * FolderUserFacade
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Facade;

use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use Zend\I18n\Translator\Translator;
use User\Api\V1\Facade\UserFacade;
use Folder\Api\V1\Facade\FolderFacade;
use Folder\Api\V1\Entity\FolderUser;
use User\Api\V1\Hydrator\UserHydrator;
use Folder\Api\V1\Hydrator\FolderUserHydrator;
use User\Api\V1\Entity\User;
use Folder\Api\V1\Entity\Folder;

class FolderUserFacade
{
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
     * @var FolderFacade
     */
    private $folderFacade;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     * @param UserFacade $userFacade
     * @param FolderFacade $folderFacade
     */
    public function __construct(
        EntityManager $entityManager,
        Translator $translator,
        UserFacade $userFacade,
        FolderFacade $folderFacade
    ) {
        $this->entityManager = $entityManager;
        $this->translator = $translator;
        $this->userFacade = $userFacade;
        $this->folderFacade = $folderFacade;
    }

    /**
     * Return the FolderUser object given a folder and a user
     *
     * @param Folder $folder
     * @param User $user
     *
     * @return FolderUser
     */
    public function getFolderUsers($folder, $user)
    {
        $folderUser = $this->entityManager
            ->getRepository(\Folder\Api\V1\Entity\FolderUser::class)
            ->findOneBy(['folder' => $folder, 'user' => $user]);
        if ($folderUser) {
            $folderUser->setAccess($folderUser->getAccess());
            return $folderUser;
        }
        return null;
    }

    /**
     * Check if a user has at least 1 manage access in general
     *
     * @param User $user
     * @return bool
     */
    public function checkUserManage($user)
    {
        $folderUsers = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findBy(["user" => $user]);

        if ($folderUsers) {
            foreach ($folderUsers as $folderUser) {
                if ($folderUser->getAccess() == 2) {
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Returns all users that have permissions on folder #
     *
     * @param int $folderId
     * @return array of User
     */
    public function getUsers($folderId)
    {
        $folder = $this->folderFacade->get($folderId);
        $folderUsers = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findBy(["folder" => $folder]);
        $userHydrator = new UserHydrator();
        if ($folderUsers) {
            $users = [];
            foreach ($folderUsers as $permessiUserFolder) {
                $realUser = new User();
                if (
                    !(
                        $permessiUserFolder->getUser() instanceof
                        User\Api\V1\Entity\User
                    )
                ) {
                    $userData = $userHydrator->extract(
                        $permessiUserFolder->getUser()
                    );
                    $realUser = $userHydrator->hydrate($userData, $realUser);
                } else {
                    $realUser = $permessiUserFolder->getUser();
                }
                $realUser->setAccess($permessiUserFolder->getAccess());
                $users[] = $realUser;
            }
            return $users;
        } else {
            return [];
        }
    }

    /**
     * Returns users without access on specified folder
     *
     * @param type $folderId
     * @return array of User
     */
    public function getUsersWithoutRights($folderId)
    {
        $usersIds = [0];
        $usersWithAccess = $this->getUsers($folderId);
        foreach ($usersWithAccess as $user) {
            $usersIds[] = $user->getUserId();
        }
        $query = $this->entityManager->createQuery(
            'SELECT u FROM User\Api\V1\Entity\User u WHERE u.userId NOT IN ( ' .
                implode(',', $usersIds) .
                '  )'
        );
        $users = $query->getResult();
        return $users;
    }

    /**
     * Check if and which permission have a user on a folder
     *
     * @param Folder $folderId
     * @param User $user
     * @return boolean|null
     */
    public function checkUser($folderId, $user)
    {
        $folder = $this->folderFacade->get($folderId);
        $folderUser = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findOneBy(["folder" => $folder, "user" => $user]);
        if ($folderUser) {
            return $folderUser->getAccess();
        }
        return null;
    }

    /**
     * Add permissions on folder # (and subfolder ?) for user #
     * Returns a FolderUser object
     *
     * @param Folder $folder
     * @param User $user
     * @param int $access
     */
    public function addUserOnFolder(Folder $folder, User $user, $access)
    {
        $this->checkAccessValue($access);

        $folderUser = new FolderUser();
        $folderUser->setFolder($folder);
        $folderUser->setUser($user);
        // verifico se esite già un record
        $rights = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findBy(["folder" => $folder, "user" => $user]);
        if (!$rights) {
            $folderUser->setAccess($access);
            $this->entityManager->persist($folderUser);
            $this->entityManager->flush();
            return $folderUser;
        } else {
            throw new ProblemDetailsException(
                422,
                sprintf(
                    $this->translator->translate(
                        'Rights on folder %s already configured for user %s'
                    ),
                    $folder->getName(),
                    $user->getUsername()
                ),
                $this->translator->translate('Unprocessable Entity'),
                'https://httpstatus.es/422'
            );
        }
    }

    /**
     * Update permissions on folder # (and subfolder ?) for user #
     * Returns a FolderUser object
     *
     * @param Folder $folder
     * @param User $user
     * @param boolean $access
     */
    public function updateUserOnFolder(Folder $folder, User $user, $access)
    {
        $this->checkAccessValue($access);

        $folderUser = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findOneBy(["folder" => $folder, "user" => $user]);
        if ($folderUser) {
            $folderUser->setAccess($access);
            $this->entityManager->persist($folderUser);
            $this->entityManager->flush();
            return $folderUser;
        } else {
            throw new ProblemDetailsException(
                422,
                sprintf(
                    $this->translator->translate(
                        'Rights on folder %s not found for user %s'
                    ),
                    $folder->getName(),
                    $user->getUsername()
                ),
                $this->translator->translate('Unprocessable Entity'),
                'https://httpstatus.es/422'
            );
        }
    }

    /**
     * Delete permission on folder # for user #
     *
     * @param Folder $folder
     * @param User $user
     * @return boolean
     * @throws ProblemDetailsException
     */
    public function deleteFolderUser(Folder $folder, User $user)
    {
        $folderUser = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findOneBy(["folder" => $folder, "user" => $user]);
        if ($folderUser) {
            $this->entityManager->remove($folderUser);
            $this->entityManager->flush();
            return true;
        } else {
            throw new ProblemDetailsException(
                422,
                sprintf(
                    $this->translator->translate(
                        'Rights on folder %s not found for user %s'
                    ),
                    $folder->getName(),
                    $user->getUsername()
                ),
                $this->translator->translate('Unprocessable Entity'),
                'https://httpstatus.es/422'
            );
        }
    }

    /**
     * Check wheter access value is accepted
     *
     * @param string $access
     * @return boolean
     * @throws ProblemDetailsException
     */
    private function checkAccessValue($access)
    {
        if (!in_array($access, [1, 2])) {
            throw new ProblemDetailsException(
                404,
                sprintf(
                    $this->translator->translate('Access type %s not found'),
                    $access
                ),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
        return true;
    }
}
