<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Facade;

use Exception;
use App\Abstracts\AbstractFacade;
use File\Api\V1\Entity\File;
use App\Service\ProblemDetailsException;
use File\Api\V1\Hydrator\FileHydrator;
use Password\Api\V1\Entity\Password;

class FileFacade extends AbstractFacade
{
    /**
     * @var FileHydrator
     */
    private $fileHydrator;

    private $uploadConfig;

    /**
     * @param \Zend\I18n\Translator\Translator $translator
     * @param \Doctrine\ORM\EntityManager $entityManager
     * @param type entityName
     * @param FileHydrator $fileHydrator
     */
    public function __construct(
        \Zend\I18n\Translator\Translator $translator,
        \Doctrine\ORM\EntityManager $entityManager,
        $entityName,
        FileHydrator $fileHydrator,
        $uploadConfig
    ) {
        $this->fileHydrator = $fileHydrator;
        $this->uploadConfig = $uploadConfig;
        parent::__construct($translator, $entityManager, $entityName);
    }
    /**
     *
     * @param array $data
     * @return string
     * @throws ProblemDetailsException
     */
    public function create($data)
    {
        try {
            $file = $this->fileHydrator->hydrate($data, new File());
            $file->setCreationDate(new \DateTime());
            $this->persist($file);
        } catch (Exception $ex) {
            throw new ProblemDetailsException(
                403,
                $this->translator->translate(
                    'Cannot create %s',
                    $this->entityName
                )
            );
        }

        return $file;
    }

    private function deleteDiskFile($file)
    {
        // delete fisical file
        $fileToDelete =
            $this->uploadConfig['upload_path'] .
            DIRECTORY_SEPARATOR .
            $file->getFilename() .
            '.' .
            'crypted';
        if (file_exists($fileToDelete)) {
            unlink($fileToDelete);
        }
    }

    /**
     *
     * @param int $id
     * @return boolean
     * @throws ProblemDetailsException
     */
    public function delete($id = null, $filter = null)
    {
        if ($id) {
            $file = $this->getRepository()->find($id);
            if ($file) {
                $this->deleteDiskFile($file);
                $this->remove($file);
            } else {
                throw new ProblemDetailsException(
                    404,
                    sprintf(
                        $this->translator->translate('%s not found'),
                        $this->entityName
                    )
                );
            }
        } else {
            $files = $this->getRepository()->findBy($filter);
            foreach ($files as $doss) {
                $this->deleteDiskFile($doss);
                $this->remove($doss);
            }
        }

        return true;
    }

    /**
     *
     * @param string $id
     * @param array $filter
     * @return File
     * @throws ProblemDetailsException
     */
    public function fetch($id = null, $filter = null)
    {
        if ($id) {
            $file = $this->getRepository()->find($id);
        } else {
            $file = $this->getRepository()->findOneBy($filter);
        }
        if ($file) {
            return $file;
        } else {
            throw new ProblemDetailsException(
                404,
                sprintf(
                    $this->translator->translate('%s not found'),
                    $this->entityName
                )
            );
        }
    }

    /**
     *
     * @param type $filter
     * @return File[]
     */
    public function fetchAll($filter = null)
    {
        if ($filter) {
            $files = $this->getRepository()->findBy($filter);
        } else {
            $files = $this->getRepository()->findAll();
        }

        return $files;
    }

    /**
     *
     * @param string $id
     * @param array $data
     * @return File
     * @throws ProblemDetailsException
     */
    public function update($id, $data)
    {
        $file = $this->getRepository()->find($id);
        if ($file) {
            $this->persist($file);
        } else {
            throw new ProblemDetailsException(
                404,
                sprintf(
                    $this->translator->translate('%s not found'),
                    $this->entityName
                )
            );
        }

        return $file;
    }

    /**
     *
     * @param string $uploadPath
     */
    public function createUploadDirectoryStructure($uploadPath)
    {
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath);
        }
    }

    /**
     * Returns all files of specified password
     *
     * @param int $id
     * @return array of Files
     */
    public function getFiles($passwordId)
    {
        $password = $this->entityManager
            ->getRepository(Password::class)
            ->find($passwordId);
        $files = $this->entityManager
            ->getRepository(File::class)
            ->findBy(["password" => $password]);
        if ($files) {
            return $files;
        }
        return [];
    }
}
