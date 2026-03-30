<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace User\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use \Folder\Api\V1\Entity\FolderUser;
use \Doctrine\Common\Collections\Collection;

/**
 * @OA\Schema(description="User")
 */
#[ORM\Entity]
#[ORM\Table(
    name: "user",
    uniqueConstraints: [
        new ORM\UniqueConstraint(
            name: "username_UNIQUE",
            columns: ["username"]
        )
    ]
)]
class User
{
    /**
     * @OA\Property
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "user_id", type: "integer")]
    private int $userId;

    /**
     * @OA\Property(example="user")
     */
    #[ORM\Column(name: "username", type: "string", length: 45, unique: true)]
    private string $username;

    /**
     * @OA\Property(property="password", type="string", description="User's password")
     */
    #[ORM\Column(name: "password", type: "string", length: 200, nullable: true)]
    private ?string $password = null;

    /**
     * @OA\Property(property="name", type="string", description="User's name")
     */
    #[ORM\Column(name: "name", type: "string", length: 45, nullable: true)]
    private ?string $name = null;

    /**
     * @OA\Property(property="surname", type="string", description="User's surname")
     */
    #[ORM\Column(name: "surname", type: "string", length: 45, nullable: true)]
    private ?string $surname = null;

    /**
     * @OA\Property(property="language", type="string", description="User's language", example="en")
     */
    #[ORM\Column(name: "language", type: "string", length: 2)]
    private string $language;

    /**
     * @OA\Property(property="phone", type="string", description="User's phone number", example="+41 91 123 45 67")
     */
    #[ORM\Column(name: "phone", type: "string", length: 45, nullable: true)]
    private ?string $phone = null;

    /**
     * @OA\Property(property="email", type="string", description="User's email", example="user@domain.com")
     */
    #[ORM\Column(name: "email", type: "string", length: 45, nullable: true, unique: true)]
    private ?string $email = null;

    /**
     * @OA\Property(property="enabled", type="boolean", description="Whether a user is enabled (true) or not (false)")
     */
     #[ORM\Column(name: "enabled", type: "boolean")]
    private bool $enabled;

    /**
     * @OA\Property(property="change_password", type="boolean", description="Whether a user need to change his password (true) or not (false)")
     */
    #[ORM\Column(name: "change_password", type: "boolean")]
    private bool $changePassword;

    /**
     * @OA\Property
     */
    #[ORM\OneToMany(targetEntity: FolderUser::class, mappedBy: "user")]
    private Collection $folder;

    private $access;

    private bool $completeUser = true;

    function getCompleteUser()
    {
        return $this->completeUser;
    }

    function setCompleteUser()
    {
        $this->completeUser = false;
    }

    public function setAccess($access = null)
    {
        $this->access = $access;
    }

    public function getAccess()
    {
        return $this->access;
    }

    private $folderId;

    public function setFolderId($folderId = null)
    {
        $this->folderId = $folderId;
    }

    public function getFolderId()
    {
        return $this->folderId;
    }

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->folder = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get language
     *
     * @return string
     */
    function getLanguage()
    {
        return $this->language;
    }

    /**
     * Set language
     *
     * @param string $language
     */
    function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get userId
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * Set userId
     *
     * @param int $userId
     * @return void
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Set username
     *
     * @param string $username
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;
        return $this;
    }

    /**
     * Get username
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password
     *
     * @param string|null $password
     * @return User
     */
    public function setPassword($password = null)
    {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string|null $name
     * @return User
     */
    public function setName($name = null)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname
     *
     * @param string|null $surname
     * @return User
     */
    public function setSurname($surname = null)
    {
        $this->surname = $surname;
        return $this;
    }

    /**
     * Get surname
     *
     * @return string|null
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set phone
     *
     * @param string|null $phone
     * @return User
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;
        return $this;
    }

    /**
     * Get phone
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email
     *
     * @param string|null $email
     * @return User
     */
    public function setEmail($email = null)
    {
        $this->email = $email;
        return $this;
    }

    /**
     * Get email
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled
     *
     * @param bool $enabled
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Get enabled
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add folder
     *
     * @param FolderUser $folder
     * @return User
     */
    public function addFolder(FolderUser $folder)
    {
        $this->folder[] = $folder;
        return $this;
    }

    /**
     * Remove folder
     *
     * @param FolderUser $folder
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFolder(FolderUser $folder)
    {
        return $this->folder->removeElement($folder);
    }

    /**
     * Get folder
     *
     * @return Collection
     */
    public function getFolder()
    {
        return $this->folder;
    }

    /**
     * Getter for changePassword
     *
     * @return bool
     */
    function getChangePassword()
    {
        return $this->changePassword;
    }

    /**
     * Setter for changePassword
     *
     * @param bool $value
     */
    function setChangePassword($value)
    {
        $this->changePassword = $value;
    }
}
