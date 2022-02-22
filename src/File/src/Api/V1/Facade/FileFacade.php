<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace File\Api\V1\Facade;

use Exception;
use App\Abstracts\AbstractFacade;
use File\Api\V1\Entity\File;
use App\Service\ProblemDetailsException;
use Doctrine\ORM\EntityManager;
use File\Api\V1\Hydrator\FileHydrator;
use Password\Api\V1\Entity\Password;
use Laminas\I18n\Translator\Translator;

class FileFacade extends AbstractFacade
{
    /**
     * @param Translator $translator
     * @param EntityManager $entityManager
     * @param type entityName
     * @param FileHydrator $fileHydrator
     * @param array $uploadConfig
     */
    public function __construct(
        Translator $translator,
        EntityManager $entityManager,
        string $entityName,
        private readonly FileHydrator $fileHydrator,
        private array $uploadConfig
    ) {
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
        } catch (Exception) {
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

    /**
     * Handles check,moving and encrypting files
     *
     * @param \Laminas\Diactoros\UploadedFile $file
     * @param array $uploadConfig
     * @param FileCipher $fileCipher
     * @param string $encriptionkey
     * @param Password $password
     *
     * @return File|Exception
     *
     */
    public function handleFile(
        $file,
        $uploadConfig,
        $fileCipher,
        $encriptionKey,
        $password
    ) {
        $realMime = mime_content_type($_FILES["file"]["tmp_name"]);
        $uploadedFileName = $_FILES["file"]["name"];

        if (
            in_array(
                $realMime,
                array_keys($uploadConfig['accepted_mime_types'])
            )
        ) {
            $filename = md5($file->getClientFilename() . time() . rand());
            $this->createUploadDirectoryStructure($uploadConfig['upload_path']);

            $path =
                $uploadConfig['upload_path'] . DIRECTORY_SEPARATOR . $filename;

            // move the file to directory
            $file->moveTo(
                $path .
                    '.' .
                    $uploadConfig['accepted_mime_types'][
                        $file->getClientMediaType()
                    ]
            );

            //encrypt file
            $fileCipher->setKey($encriptionKey);
            if (
                $fileCipher->encrypt(
                    $path .
                        '.' .
                        $uploadConfig['accepted_mime_types'][
                            $file->getClientMediaType()
                        ],
                    $path . '.' . 'crypted'
                )
            ) {
                //remove non crypted file
                unlink(
                    $path .
                        '.' .
                        $uploadConfig['accepted_mime_types'][
                            $file->getClientMediaType()
                        ]
                );
            }

            $file = $this->create([
                'password' => $password,
                'filename' => $filename,
                'name' => $uploadedFileName,
                'extension' => $realMime
            ]);

            return $file;
        } else {
            throw new ProblemDetailsException(
                400,
                $this->translator->translate('Mime type not allowed')
            );
        }
    }

    /**
     * Delete the phisical file
     *
     * @param File $file
     */
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
