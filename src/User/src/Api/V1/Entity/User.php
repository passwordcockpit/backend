<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace User\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * User
 *
 * @ORM\Table(name="user", uniqueConstraints={@ORM\UniqueConstraint(name="username_UNIQUE", columns={"username"})})
 * @ORM\Entity
 * @SWG\Definition(definition="User")
 */
class User
{
    /**
     * @var int
     *
     * @ORM\Column(name="user_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     *
     * @SWG\Property
     */
    private $userId;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="string", length=45, precision=0, scale=0, nullable=false, unique=true)
     *
     * @SWG\Property(example="user")
     */
    private $username;

    /**
     * @var string|null
     *
     * @ORM\Column(name="password", type="string", length=200, precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="password", type="string", description="User's password")
     */
    private $password;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="name", type="string", description="User's name")
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="surname", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="surname", type="string", description="User's surname")
     */
    private $surname;

    /**
     * @var string
     *
     * @ORM\Column(name="language", type="string", length=2, precision=0, scale=0, nullable=false, unique=false)
     *
     * @SWG\Property(property="language", type="string", description="User's language", example="en")
     */
    private $language;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phone", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="phone", type="string", description="User's phone number", example="+41 91 123 45 67")
     */
    private $phone;

    /**
     * @var string|null
     *
     * @ORM\Column(name="email", type="string", length=45, precision=0, scale=0, nullable=true, unique=true)
     *
     * @SWG\Property(property="email", type="string", description="User's email", example="user@domain.com")
     */
    private $email;

    /**
     * @var bool
     *
     * @ORM\Column(name="enabled", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     * @SWG\Property(property="enabled", type="boolean", description="Whether a user is enabled (true) or not (false)")
     */
    private $enabled;

    /**
     * @var bool
     *
     * @ORM\Column(name="change_password", type="boolean", precision=0, scale=0, nullable=false, unique=false)
     * @SWG\Property(property="change_password", type="boolean", description="Whether a user need to change his password (true) or not (false)")
     */
    private $changePassword;

    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Folder\Api\V1\Entity\FolderUser", mappedBy="user")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *
     * @SWG\Property
     */
    private $folder;

    private $access;

    /**
     *
     * @var boolean
     */
    private $completeUser = true;

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

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->folder = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Getter for language
     *
     * @return string
     */
    function getLanguage()
    {
        return $this->language;
    }

    /**
     * Setter for language
     *
     * @param string $language
     */
    function setLanguage($language)
    {
        $this->language = $language;
    }

    /**
     * Get userId.
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * Set username.
     *
     * @param string $username
     *
     * @return User
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set password.
     *
     * @param string|null $password
     *
     * @return User
     */
    public function setPassword($password = null)
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return string|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return User
     */
    public function setName($name = null)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set surname.
     *
     * @param string|null $surname
     *
     * @return User
     */
    public function setSurname($surname = null)
    {
        $this->surname = $surname;

        return $this;
    }

    /**
     * Get surname.
     *
     * @return string|null
     */
    public function getSurname()
    {
        return $this->surname;
    }

    /**
     * Set phone.
     *
     * @param string|null $phone
     *
     * @return User
     */
    public function setPhone($phone = null)
    {
        $this->phone = $phone;

        return $this;
    }

    /**
     * Get phone.
     *
     * @return string|null
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Set email.
     *
     * @param string|null $email
     *
     * @return User
     */
    public function setEmail($email = null)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string|null
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set enabled.
     *
     * @param bool $enabled
     *
     * @return User
     */
    public function setEnabled($enabled)
    {
        $this->enabled = $enabled;

        return $this;
    }

    /**
     * Get enabled.
     *
     * @return bool
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * Add folder.
     *
     * @param \Folder\Api\V1\Entity\FolderUser $folder
     *
     * @return User
     */
    public function addFolder(\Folder\Api\V1\Entity\FolderUser $folder)
    {
        $this->folder[] = $folder;

        return $this;
    }

    /**
     * Remove folder.
     *
     * @param \Folder\Api\V1\Entity\FolderUser $folder
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeFolder(\Folder\Api\V1\Entity\FolderUser $folder)
    {
        return $this->folder->removeElement($folder);
    }

    /**
     * Get folder.
     *
     * @return \Doctrine\Common\Collections\Collection
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
