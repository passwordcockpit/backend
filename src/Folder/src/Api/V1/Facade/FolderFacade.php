<?php

/**
 * FolderFacade
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Facade;

use App\Abstracts\AbstractFacade;
use Folder\Api\V1\Entity\Folder;
use Folder\Api\V1\Entity\FolderUser;
use User\Api\V1\Entity\User;
use User\Api\V1\Facade\UserFacade;
use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\Translator;
use Psr\Http\Message\ServerRequestInterface;
use App\Service\ProblemDetailsException;
use Password\Api\V1\Entity\Password;

class FolderFacade extends AbstractFacade
{
    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     * @param UserFacade $userFacade
     */
    public function __construct(
        protected EntityManager $entityManager,
        protected Translator $translator,
        private readonly UserFacade $userFacade
    ) {
        parent::__construct($translator, $entityManager, Folder::class);
    }

    /**
     *
     * @param array $data
     */
    public function create($data): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $filter
     */
    public function fetch($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param array $filter
     */
    public function fetchAll($filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $data
     */
    public function update($id, $data): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param type $id
     * @param type $filter
     */
    public function delete($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     * Create a new folder
     *
     * @param ServerRequestInterface $request
     * @return User
     */
    public function createFolder(ServerRequestInterface $request)
    {
        $payload = $request->getParsedBody();
        $folder = new Folder();
        $folder->setAccess(true);
        $folder->setName($payload['name']);
        $user = $request->getAttribute('Authentication\User');
        if ($payload['parent_id'] != null) {
            $parentId = $payload['parent_id'];
            $parent = $this->entityManager
                ->getRepository(Folder::class)
                ->find($parentId);
            if ($parent) {
                $folder->setParentId($parentId);
            } else {
                throw new ProblemDetailsException(
                    404,
                    $this->translator->translate('Parent folder not found'),
                    $this->translator->translate('Resource not found'),
                    'https://httpstatus.es/404'
                );
            }
        }
        $this->entityManager->persist($folder);
        // add access on the folder for user that create it
        $folderUser = new FolderUser();
        $folderUser->setAccess(2);
        $folderUser->setUser($user);
        $folderUser->setFolder($folder);
        $this->entityManager->persist($folderUser);
        $this->entityManager->flush();
        return $folder;
    }

    /**
     * Delete folder by id
     *
     * @param int $id
     * @return boolean
     * @throws ProblemDetailsException
     */
    public function deleteFolder($id)
    {
        $folder = $this->entityManager->getRepository(Folder::class)->find($id);
        if ($folder) {
            // check if folder has password
            $passwords = $this->entityManager
                ->getRepository(Password::class)
                ->findBy(['folder' => $folder]);
            if ($passwords) {
                throw new ProblemDetailsException(
                    422,
                    sprintf(
                        $this->translator->translate(
                            'Folder %s contains passwords'
                        ),
                        $folder->getName()
                    ),
                    $this->translator->translate('Delete not possible'),
                    'https://httpstatus.es/422'
                );
            }
            // check if folder has subfolder
            $subfolders = $this->entityManager
                ->getRepository(Folder::class)
                ->findBy(['parentId' => $folder->getFolderId()]);
            if ($subfolders) {
                throw new ProblemDetailsException(
                    422,
                    sprintf(
                        $this->translator->translate(
                            'Folder %s has subfolders'
                        ),
                        $folder->getName()
                    ),
                    $this->translator->translate('Delete not possible'),
                    'https://httpstatus.es/422'
                );
            }

            foreach ($folder->getUser() as $folderUser) {
                $this->entityManager->remove($folderUser);
            }
            $this->entityManager->remove($folder);
            $this->entityManager->flush();
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Folder not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
        return true;
    }

    /**
     * Returns all folders
     *
     * @return array of Folder
     */
    public function getAll()
    {
        $folders = $this->entityManager
            ->getRepository(Folder::class)
            ->findBy([], ['name' => 'ASC']);
        return $folders;
    }

    /**
     * Returns all folders that have the title equal to the param
     *
     * @param string $searchString
     * @return array of Folder
     */
    public function getAllByName($searchString)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('f')
            ->from(Folder::class, 'f')
            ->where('f.name like ?1')
            ->orderBy('f.name', 'ASC')
            ->setParameter(1, '%' . $searchString . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all folders that have the title equal to the param and user has access
     *
     * @param string $string
     * @return array of Folder
     */
    public function getByName($searchString, $userId)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('folder')
            ->from(FolderUser::class, 'fu')
            ->join(Folder::class, 'folder', 'WITH', 'fu.folder=folder')
            ->join('fu.user', 'user')
            ->where('folder.name like ?1')
            ->andWhere(
                $queryBuilder
                    ->expr()
                    ->andX(
                        $queryBuilder->expr()->eq('user.userId', '?2'),
                        $queryBuilder
                            ->expr()
                            ->orX(
                                $queryBuilder->expr()->eq('fu.access', '1'),
                                $queryBuilder->expr()->eq('fu.access', '2')
                            )
                    )
            )
            ->setParameter(1, '%' . $searchString . '%')
            ->setParameter(2, $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * set right access to the folders based on userId
     *
     * @param array of Folder
     * @param int $userId
     * @return array of Folder
     *
     */
    public function setAccessToFolders($folders, $userId)
    {
        $user = $this->userFacade->get($userId);

        foreach ($folders as $fold) {
            $folderUser = $this->entityManager
                ->getRepository(FolderUser::class)
                ->findBy(['folder' => $fold, 'user' => $user]);

            if ($folderUser) {
                // since folder user is an array, we just need the first element
                $access = $folderUser[0]->getAccess();
            } else {
                $access = null;
            }

            $fold->setAccess($access);
        }
        return $folders;
    }

    /**
     * Recover all folders where user has access
     *
     * @param User $user
     * @return array of Folder
     */
    public function getAllFiltered($user)
    {
        $folderUser = $this->entityManager
            ->getRepository(FolderUser::class)
            ->findBy(['user' => $user]);
        if ($folderUser) {
            $folders = [];
            foreach ($folderUser as $permessiUserFolder) {
                $realFolder = new Folder();
                $realFolder = $permessiUserFolder->getFolder();
                $realFolder->setAccess($permessiUserFolder->getAccess());
                $folders[] = $realFolder;
            }
            return $folders;
        }
        return [];
    }

    /**
     * Returns all folders where user has access AND all their parents
     *
     * @param array $folders
     * @return array of Folder
     */
    public function generateTree($folders)
    {
        $viewableFolders = [];
        foreach ($folders as $folder) {
            $viewableFolders[$folder->getFolderId()] = $folder;
            while (
                $folder->getParentId() &&
                !isset($viewableFolders[$folder->getParentId()])
            ) {
                $folder = $this->entityManager
                    ->getRepository(Folder::class)
                    ->find($folder->getParentId());
                $viewableFolders[$folder->getFolderId()] = $folder;
            }
        }
        return $viewableFolders;
    }

    /**
     * Returns folder by id
     *
     * @param int $id
     * @return Folder
     * @throws ProblemDetailsException
     */
    public function get($id)
    {
        $folder = $this->entityManager->getRepository(Folder::class)->find($id);
        if ($folder) {
            return $folder;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Folder not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Update a folder
     *
     * @param int $id
     * @param ServerRequestInterface $request
     * @return Folder
     * @throws ProblemDetailsException
     */
    public function updateFolder($id, ServerRequestInterface $request)
    {
        $folder = $this->entityManager->getRepository(Folder::class)->find($id);
        if ($folder) {
            $payload = $request->getParsedBody();

            if (isset($payload['name'])) {
                $folder->setName($payload['name']);
            }
            if (isset($payload['parent_id'])) {
                $parent = $this->entityManager
                    ->getRepository(Folder::class)
                    ->find($payload['parent_id']);
                if ($parent) {
                    $folder->setParentId($payload['parent_id']);
                } else {
                    throw new ProblemDetailsException(
                        404,
                        $this->translator->translate('Parent folder not found'),
                        $this->translator->translate('Resource not found'),
                        'https://httpstatus.es/404'
                    );
                }
            }
            $this->entityManager->persist($folder);
            $this->entityManager->flush();
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Folder not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
        $folder = $this->entityManager
            ->getRepository(Folder::class)
            ->find($folder->getFolderId());
        return $folder;
    }
}
