<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Folder\Api\V1\Hydrator;

/**
 * Description of FolderHalHydrator
 */
use Folder\Api\V1\Entity\Folder;
use Laminas\Hydrator\AbstractHydrator;

class FolderHalHydrator extends AbstractHydrator
{
    /**
     *
     * @param Folder $folder
     * @return array
     */
    public function extract($folder): array
    {
        $data = [];
        $data['folder_id'] = $folder->getFolderId();
        $data['parent_id'] = $folder->getParentId();
        $data['name'] = $folder->getName();
        //switch from read to 1 and manage to 2 or null
        if ($folder->getAccess() == 1) {
            $data['access'] = 1;
        } elseif ($folder->getAccess() == 2) {
            $data['access'] = 2;
        } else {
            $data['access'] = $folder->getAccess();
        }
        return $data;
    }

    /**
     *
     * @param array $data
     * @param Folder $folder
     * @return Folder
     */
    public function hydrate(array $data, $folder)
    {
    }

    /**
     * Check whether a property is available for the object
     *
     * @param string $property
     * @param array $data
     * @return bool
     */
    protected function isPropertyAvailable($property, $data)
    {
        return isset($data[$property]) && $data[$property] !== '';
    }
}
