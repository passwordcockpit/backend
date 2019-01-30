<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Hydrator;

use File\Api\V1\Entity\File;

class FileHydrator extends \Zend\Hydrator\AbstractHydrator
{
    /**
     * @param MimeTypeRepository $mimeTypeRepository
     */
    public function __construct()
    {
    }
    /**
     * Extract informations from File object
     *
     * @param File $object
     *
     * @return array
     */
    public function extract($object): array
    {
        $data = [];
        $data['file_id'] = $object->getFileId();
        $data['password_id'] = $object->getPassword()->getPasswordId();
        $data['mime_type'] = $object->getExtension();
        $data['creation_date'] = \App\Service\DateConverter::formatDateTime(
            $object->getCreationDate(),
            'outputDate'
        );
        $data['name'] = $object->getName();
        $data['filename'] = $object->getFilename();

        return $data;
    }

    /**
     *
     * @param array $data
     * @param File $object
     * @return File
     * @throws \Exception
     */
    public function hydrate(array $data, $object)
    {
        if (!$object instanceof File) {
            throw new \Exception("Wrong object to hydrate");
        }
        if (isset($data['fileId'])) {
            $object->setFileId($data['fileId']);
        }
        if (isset($data['password'])) {
            $object->setPassword($data['password']);
        }
        if (isset($data['creationDate'])) {
            $object->setCreationDate($data['creationDate']);
        }
        if (isset($data['name'])) {
            $object->setName($data['name']);
        }
        if (isset($data['filename'])) {
            $object->setFilename($data['filename']);
        }
        if (isset($data['extension'])) {
            $object->setExtension($data['extension']);
        }

        return $object;
    }
}
