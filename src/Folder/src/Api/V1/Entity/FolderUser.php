<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Folder\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use User\Api\V1\Entity\User;

/**
 * @OA\Schema(description="FolderUser")
 */
#[ORM\Entity]
#[ORM\Table(name: "folder_user")]
class FolderUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "folder_user_id", type: "integer")]
    private int $folderUserId;

    #[ORM\Column(name: "access", type: "smallint", nullable: true)]
    private int $access;

    #[ORM\ManyToOne(targetEntity: Folder::class, inversedBy: "user", fetch: "EAGER")]
    #[ORM\JoinColumn(name: "folder_id", referencedColumnName: "folder_id", nullable: true, onDelete: "CASCADE")]
    private Folder $folder;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: "folder", fetch: "EAGER")]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", nullable: true, onDelete: "CASCADE")]
    private User $user;

    /**
     * Get folderUserId
     *
     * @return int
     */
    public function getFolderUserId()
    {
        return $this->folderUserId;
    }

    /**
     * Set access
     *
     * @param string|null $access
     * @return FolderUser
     */
    public function setAccess($access = null)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * Get access
     *
     * @return string|null
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set folder
     *
     * @param Folder|null $folder
     * @return FolderUser
     */
    public function setFolder(?Folder $folder)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder
     * 
     * @return Folder|null
     */
    public function getFolder()
    {
        //$this->folder = $this->folder->__load();
        return $this->folder;
    }

    /**
     * Set user
     *
     * @param User|null $user
     * @return FolderUser
     */
    public function setUser(?User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User|null
     */
    public function getUser()
    {
        return $this->user;
    }
}
