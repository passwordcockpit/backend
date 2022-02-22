<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Folder\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;

/**
 * FolderUser
 *
 * @ORM\Table(name="folder_user")
 * @ORM\Entity
 * @OA\Schema(description="FolderUser")
 */
class FolderUser
{
    /**
     * @var int
     *
     * @ORM\Column(name="folder_user_id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $folderUserId;

    /**
     * @var int
     *
     * @ORM\Column(name="access", type="smallint", precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property
     */
    private $access;

    /**
     * @var \Folder\Api\V1\Entity\Folder
     *
     * @ORM\ManyToOne(targetEntity="Folder\Api\V1\Entity\Folder", inversedBy="user", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="folder_id", referencedColumnName="folder_id", nullable=true)
     * })
     */
    private $folder;

    /**
     * @var User\Api\V1\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="User\Api\V1\Entity\User", inversedBy="folder", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=true)
     * })
     */
    private $user;

    /**
     * Get folderUserId.
     *
     * @return int
     */
    public function getFolderUserId()
    {
        return $this->folderUserId;
    }

    /**
     * Set access.
     *
     * @param string|null $access
     *
     * @return FolderUser
     */
    public function setAccess($access = null)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * Get access.
     *
     * @return string|null
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set folder.
     *
     * @param \Folder\Api\V1\Entity\Folder|null $folder
     *
     * @return FolderUser
     */
    public function setFolder(\Folder\Api\V1\Entity\Folder $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder.
     *
     * @return \Folder\Api\V1\Entity\Folder|null
     */
    public function getFolder()
    {
        //$this->folder = $this->folder->__load();
        return $this->folder;
    }

    /**
     * Set user.
     *
     * @param \User\Api\V1\Entity\User|null $user
     *
     * @return FolderUser
     */
    public function setUser(\User\Api\V1\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return User\Api\V1\Entity\User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
