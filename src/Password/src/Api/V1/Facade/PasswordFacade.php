<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Password\Api\V1\Facade;

use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\Translator;
use Password\Api\V1\Entity\Password;
use App\Service\ProblemDetailsException;
use Laminas\Crypt\BlockCipher;
use Laminas\Crypt\FileCipher;
use Password\Api\V1\Hydrator\PasswordHydrator;
use Psr\Http\Message\ServerRequestInterface;
use Folder\Api\V1\Entity\Folder;
use Folder\Api\V1\Entity\FolderUser;
use Folder\Api\V1\Facade\FolderFacade;
use File\Api\V1\Facade\FileFacade;
use Log\Api\V1\Entity\Log;
use User\Api\V1\Entity\User;
use Log\Api\V1\Facade\LogFacade;
use File\Api\V1\Entity\File;
use App\Abstracts\AbstractFacade;

class PasswordFacade extends AbstractFacade
{
    private int $userId;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param Translator $translator
     * @param BlockCipher $blockCipher
     * @param FileCipher $fileCipher
     * @param string $encriptionKey
     * @param FolderFacade $folderFacade
     * @param LogFacade $logFacade
     * @param FileFacade $fileFacade
     * @param array $uploadConfig
     */
    public function __construct(
        protected EntityManager $entityManager,
        protected Translator $translator,
        private readonly BlockCipher $blockCipher,
        private readonly FileCipher $fileCipher,
        private $encriptionKey,
        private readonly FolderFacade $folderFacade,
        private readonly LogFacade $logFacade,
        private readonly FileFacade $fileFacade,
        private $uploadConfig
    ) {
        parent::__construct($translator, $entityManager, Password::class);
    }

    /**
     * Set the userId to be used for logging purpose
     *
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    private function getUserId()
    {
        return $this->userId;
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
     * @param type $id
     * @param type $filter
     */
    public function delete($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     * Create new instance of Password
     *
     * @param array $data
     * @return Password
     */
    public function create($data)
    {
        $password = $this->reflectionHydrator->hydrate($data, new Password());
        $this->persist($password);

        return $password;
    }

    /**
     * Create a new password
     *
     * @param ServerRequestInterface $request
     * @return Password
     * @throws ProblemDetailsException
     */
    public function createPassword(ServerRequestInterface $request)
    {
        $payload = $request->getParsedBody();
        $password = new Password();
        $folder = $this->entityManager
            ->getRepository(Folder::class)
            ->find($payload['folder_id']);
        if ($folder) {
            $passwordHydrator = new PasswordHydrator();
            $password = $passwordHydrator->hydrate($payload, $password);
            $password->setFolder($folder);
            if (isset($payload['password']) && !$payload['password'] == "") {
                $password->setPassword(
                    $this->encrypt($password->getPassword())
                );
            }
            $password->setLastModificationDate(new \DateTime());
            $this->entityManager->persist($password);
            $this->entityManager->flush();
            //$this->updateLog($password->getPasswordId(), "Password created");
            $this->logFacade->updateLog(
                $password->getPasswordId(),
                "Password created",
                $this->getUserId()
            );

            // Check if file was uploaded
            if (isset($request->getUploadedFiles()['file'])) {
                $file = $request->getUploadedFiles()['file'];

                // handle physical file
                $file = $this->fileFacade->handleFile(
                    $file,
                    $this->uploadConfig,
                    $this->fileCipher,
                    $this->encriptionKey,
                    $password
                );

                $password->setFileId($file->getFileId());
                $password->setFileName($file->getName());
            }
            if (isset($payload['password'])) {
                $password->setPassword($payload['password']);
            }
            return $password;
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
     * Move a password into another folder
     *
     * @param $passwordId
     * @param $folderId
     *
     */
    public function movePassword($passwordId, $folderId)
    {
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($passwordId);

        $folder = $this->entityManager
            ->getRepository(Folder::class)
            ->find($folderId);

        if ($password && $folder) {
            $password->setFolder($folder);
            $this->entityManager->persist($password);
            $this->entityManager->flush();

            return $password;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Resource not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
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
     * Update a password
     *
     * @param int $id
     * @param ServerRequestInterface $request
     * @return Password
     * @throws ProblemDetailsException
     */
    public function updatePassword($id, ServerRequestInterface $request)
    {
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($id);
        if ($password) {
            $passwordHydrator = new PasswordHydrator();
            $payload = $request->getParsedBody();
            $password = $passwordHydrator->hydrate($payload, $password);
            if (isset($payload['password']) && !$payload['password'] == "") {
                $password->setPassword(
                    $this->encrypt($password->getPassword())
                );
            }
            $password->setLastModificationDate(new \DateTime());
            $this->entityManager->persist($password);
            $this->entityManager->flush();
            $password = $this->entityManager
                ->getRepository(Password::class)
                ->find($password->getPasswordId());
            //$this->updateLog($id, "Password modified");
            $this->logFacade->updateLog(
                $id,
                "Password modified",
                $this->getUserId()
            );

            //check if there is a file associated still.
            $fileStill = $this->fileFacade->getFiles($id);
            if (isset($fileStill[0])) {
                $password->setFileId($fileStill[0]->getFileId());
                $password->setFileName($fileStill[0]->getName());
            }

            if (isset($payload['password'])) {
                $password->setPassword($payload['password']);
            } elseif ($password->getPassword()) {
                $password->setPassword(
                    $this->decrypt($password->getPassword())
                );
            }

            return $password;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Password not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Delete Password by id
     *
     * @param int $id
     * @return boolean
     * @throws ProblemDetailsException
     */
    public function deletePassword($id, $userId)
    {
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($id);
        if ($password) {
            // delete logs
            $logs = $this->logFacade->getPasswordLogFromPass($password);
            foreach ($logs as $log) {
                $this->entityManager->remove($log);
            }
            // delete files
            $files = $this->entityManager
                ->getRepository(File::class)
                ->findBy(['password' => $password]);
            foreach ($files as $file) {
                //remove file
                $this->fileFacade->delete($file->getFileId());
            }

            // create a "deleted password nr#" Log.
            $user = $this->entityManager
                ->getRepository(User::class)
                ->find($userId);
            $this->logFacade->createDeletedLog($password, $user);

            $this->entityManager->remove($password);
            $this->entityManager->flush();
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Password not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
        return true;
    }

    /**
     * Returns a Password by id
     *
     * @param int $id
     * @return Password
     * @throws ProblemDetailsException
     */
    public function get($id)
    {
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($id);
        if ($password) {
            $encryptedPassword = $password->getPassword();
            if ($encryptedPassword || !($encryptedPassword == "")) {
                $password->setPassword($this->decrypt($encryptedPassword));
            }
            $this->entityManager->detach($password);
            //$this->updateLog($id, "Password viewed");
            $this->logFacade->updateLog(
                $id,
                "Password viewed",
                $this->getUserId()
            );
            return $password;
        } else {
            throw new ProblemDetailsException(
                404,
                $this->translator->translate('Password not found'),
                $this->translator->translate('Resource not found'),
                'https://httpstatus.es/404'
            );
        }
    }

    /**
     * Returns all passwords in folder #
     *
     * @param int $id
     * @return array of Password
     */
    public function getPasswords($folderId)
    {
        $folder = $this->folderFacade->get($folderId);
        $passwords = $this->entityManager
            ->getRepository(Password::class)
            ->findBy(['folder' => $folder], ['title' => 'ASC']);
        if ($passwords) {
            return $passwords;
        }
        return [];
    }

    /**
     * Returns all passwords that matches the string in all camps excluded 'password'
     *
     * @param string $searchString
     * @return array of Password
     */
    public function getAllPasswordsbySearch($searchString)
    {
        // this query builder checks all the fields below and return the passwords
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('f')
            ->from(Password::class, 'f')
            ->where('f.title like ?1')
            ->orWhere('f.description like ?1')
            ->orWhere('f.username like ?1')
            ->orWhere('f.url like ?1')
            ->orWhere('f.tags like ?1')
            ->setParameter(1, '%' . $searchString . '%')
            ->getQuery()
            ->getResult();
    }

    /**
     * Returns all passwords that matches the string in all camps excluded 'password'
     *
     * @param string $searchString
     * @return array of FolderUser
     */
    public function getPasswordsbySearch($searchString, $userId)
    {
        // this query builder checks all the fields below and return the passwords
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('pw')
            ->from(FolderUser::class, 'fu')
            ->join('fu.folder', 'f')
            ->join(Password::class, 'pw', 'WITH', 'pw.folder=fu.folder')
            ->join('fu.user', 'user')
            ->where(
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
            ->andWhere(
                $queryBuilder
                    ->expr()
                    ->orX(
                        $queryBuilder->expr()->like('pw.title', '?1'),
                        $queryBuilder->expr()->like('pw.description', '?1'),
                        $queryBuilder->expr()->like('pw.username', '?1'),
                        $queryBuilder->expr()->like('pw.url', '?1'),
                        $queryBuilder->expr()->like('pw.tags', '?1')
                    )
            )
            ->setParameter(1, '%' . $searchString . '%')
            ->setParameter(2, $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * Associate a password array with their files.
     *
     * @param array of Password
     * @return array of Password
     */
    public function associatePasswordsFiles($passwords)
    {
        foreach ($passwords as $pass) {
            $fileStill = $this->fileFacade->getFiles($pass->getPasswordId());
            if (isset($fileStill[0])) {
                $pass->setFileId($fileStill[0]->getFileId());
                $pass->setFileName($fileStill[0]->getName());
            }
        }
        return $passwords;
    }

    /**
     * Returns decrypted password
     *
     * @param string $encrypted
     * @return string
     */
    public function decrypt($encrypted)
    {
        $this->blockCipher->setKey($this->encriptionKey);
        if ($encrypted) {
            $encrypted = $this->blockCipher->decrypt($encrypted);
        }

        return $encrypted;
    }

    /**
     * Returns encrpyted password
     *
     * @param string $password
     * @return string
     */
    private function encrypt($password)
    {
        $this->blockCipher->setKey($this->encriptionKey);
        return $this->blockCipher->encrypt($password);
    }

    /**
     * Update the log for the specified password
     *
     * @param Password $password
     * @param string $action
     * @return boolean
     */
    public function updateLog($passwordId, $action)
    {
        $log = new Log();
        $log->setAction($action);
        $pass = $this->entityManager->getReference(
            Password::class,
            $passwordId
        );
        $log->setPassword($pass);
        $log->setActionDate(new \DateTime());
        $user = $this->entityManager
            ->getRepository(User::class)
            ->find($this->getUserId());
        $log->setUser($user);
        $this->entityManager->persist($log);
        $this->entityManager->flush();

        return true;
    }
}
