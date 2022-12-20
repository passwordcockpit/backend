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
 * Folder
 *
 * @ORM\Table(name="folder")
 * @ORM\Entity
 * @OA\Schema(description="Folder")
 */
class Folder
{
    /**
     *
     * @ORM\Column(name="folder_id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue
     * @OA\Property
     */
    private int $folderId;

    /**
     *
     * @ORM\Column(name="name", type="string", length=45, precision=0, scale=0, nullable=false, unique=false)
     * @OA\Property(example="folderName")
     */
    private string $name;

    /**
     *
     * @ORM\Column(name="parent_id", type="integer", precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="parent_id", example=null)
     */
    private ?int $parentId = null;

    /**
     *
     * @ORM\OneToMany(targetEntity="Folder\Api\V1\Entity\FolderUser", mappedBy="folder")
     * @ORM\JoinColumn(name="folder_id", referencedColumnName="folder_id", onDelete="CASCADE")
     * @OA\Property
     */
    private \Doctrine\Common\Collections\Collection $user;

    private $access;
    public function setAccess($access = null)
    {
        $this->access = $access;
    }
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get folderId.
     *
     * @return int
     */
    public function getFolderId()
    {
        return $this->folderId;
    }

    /**
     * Set folderId
     *
     * @param int $folderId
     * @return Folder
     */
    public function setFolderId($folderId)
    {
        $this->folderId = $folderId;

        return $this;
    }

    /**
     * Set name.
     *
     * @param string $name
     *
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set parentId.
     *
     * @param int|null $parentId
     *
     * @return Folder
     */
    public function setParentId($parentId = null)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId.
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Add user.
     *
     * @param FolderUser $user
     *
     * @return Folder
     */
    public function addUser(FolderUser $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user.
     *
     * @param FolderUser $user
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUser(FolderUser $user)
    {
        return $this->user->removeElement($user);
    }

    /**
     * Get user.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUser()
    {
        return $this->user;
    }
}
