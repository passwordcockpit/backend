<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Folder\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use \Doctrine\Common\Collections\Collection;
use \Doctrine\Common\Collections\ArrayCollection;

/**
 * @OA\Schema(description="Folder")
 */
#[ORM\Entity]
#[ORM\Table(name: "folder")]
class Folder
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "folder_id", type: "integer")]
    private int $folderId;

    /**
     * @OA\Property(example="folderName")
     */
    #[ORM\Column(name: "name", type: "string", length: 45)]
    private string $name;

    /**
     * @OA\Property(property="parent_id", example=null)
     */
    #[ORM\Column(name: "parent_id", type: "integer", nullable: true)]
    private ?int $parentId = null;

    /**
     * @OA\Property
     */
    #[ORM\OneToMany(mappedBy: "folder", targetEntity: FolderUser::class)]
    private Collection $user;

    private $access;

    /**
     * Set access
     * 
     * @param int|null $access
     * @return Folder
     */
    public function setAccess($access = null)
    {
        $this->access = $access;
    }

    /**
     * Get access
     *
     * @return int|null
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->user = new ArrayCollection();
    }

    /**
     * Get folderId
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
     * Set name
     *
     * @param string $name
     * @return Folder
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set parentId
     *
     * @param int|null $parentId
     * @return Folder
     */
    public function setParentId($parentId = null)
    {
        $this->parentId = $parentId;

        return $this;
    }

    /**
     * Get parentId
     *
     * @return int|null
     */
    public function getParentId()
    {
        return $this->parentId;
    }

    /**
     * Add user
     *
     * @param FolderUser $user
     * @return Folder
     */
    public function addUser(FolderUser $user)
    {
        $this->user[] = $user;

        return $this;
    }

    /**
     * Remove user
     *
     * @param FolderUser $user
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeUser(FolderUser $user)
    {
        return $this->user->removeElement($user);
    }

    /**
     * Get user
     *
     * @return Collection
     */
    public function getUser()
    {
        return $this->user;
    }
}
