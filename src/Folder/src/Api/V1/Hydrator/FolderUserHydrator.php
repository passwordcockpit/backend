<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace Folder\Api\V1\Hydrator;

use Laminas\Hydrator\AbstractHydrator;

/**
 * Description of FolderUserHydrator
 */
class FolderUserHydrator extends AbstractHydrator
{
    /**
     * Extract fields from FolderUser object and create array
     *
     * @param FolderUser $folderUser
     * @return array
     */
    public function extract($folderUser)
    {
        $data = [];
        $data['folder_user_id'] = $folderUser->getFolderUserId();
        $data['user_id'] = $folderUser->getUser()->getUserId();
        $data['folder_id'] = $folderUser->getFolder()->getFolderId();
        $data['access'] = $folderUser->getAccess();
        return $data;
    }

    public function hydrate(array $data, $object)
    {
    }
}
