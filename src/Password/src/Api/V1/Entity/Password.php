<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Password\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Folder\Api\V1\Entity\Folder;
use OpenApi\Annotations as OA;

/**
 * Password
 *
 * @ORM\Table(name="password")
 * @ORM\Entity
 * @OA\Schema(description="Password")
 */
class Password
{
    /**
     *
     * @ORM\Column(name="password_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     * @OA\Property
     */
    private int $passwordId;

    /**
     *
     * @ORM\Column(name="title", type="string", length=100, precision=0, scale=0, nullable=false, unique=false)
     * @OA\Property(property="title", type="string", description="Password's title", example="title")
     */
    private string $title;

    /**
     *
     * @ORM\Column(name="icon", type="string", length=45, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="icon", type="string", description="Password's icon", example="icon")
     */
    private ?string $icon = null;

    /**
     *
     * @ORM\Column(name="description", type="string", length=4000, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="description", type="string", description="Password's description", example="description")
     */
    private ?string $description = null;

    /**
     *
     * @ORM\Column(name="username", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="username", type="string", description="Password's username", example="username")
     */
    private ?string $username = null;

    /**
     *
     * @ORM\Column(name="password", type="string", length=1000, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="password", type="string", description="Password's password", example="password")
     */
    private ?string $password = null;

    /**
     *
     * @ORM\Column(name="url", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="url", type="string", description="Password's url", example="http://www.blackpoints.ch")
     */
    private ?string $url = null;

    /**
     *
     * @ORM\Column(name="tags", type="string", length=400, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="tags", type="string", description="Password's tags", example="tag1 tag2 tag3")
     */
    private ?string $tags = null;

    /**
     *
     * @ORM\Column(name="last_modification_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property
     */
    private ?\DateTime $lastModificationDate = null;

    /**
     *
     * @ORM\ManyToOne(targetEntity="Folder\Api\V1\Entity\Folder", fetch="EAGER")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="folder_id", referencedColumnName="folder_id", nullable=true)
     * })
     *
     * @OA\Property(property="folder_id", example=4)
     */
    private Folder $folder;

    /**
     *
     * @ORM\Column(name="frontend_crypted", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property
     */
    private bool $frontendCrypted;

    private bool $completePassword = true;

    private ?int $fileId = null;

    private ?string $fileName = null;

    /**
     * @return bool
     */
    function getfrontendCrypted()
    {
        return $this->frontendCrypted;
    }

    /**
     * @param bool $frontendCrypted
     */
    function setfrontendCrypted($frontendCrypted)
    {
        $this->frontendCrypted = $frontendCrypted;
    }

    /**
     * @return int
     */
    function getFileId()
    {
        return $this->fileId;
    }

    /**
     * @return string
     */
    function getFileName()
    {
        return $this->fileName;
    }

    /**
     * @param int $fileId
     */
    function setFileId($fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @param string $fileName
     */
    function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }

    /**
     *
     * @return boolean
     */
    function getCompletePassword()
    {
        return $this->completePassword;
    }

    /**
     *
     */
    function setCompletePassword()
    {
        $this->completePassword = false;
    }

    /**
     * Get passwordId.
     *
     * @return int
     */
    public function getPasswordId()
    {
        return $this->passwordId;
    }

    /**
     * Set title.
     *
     * @param string|null $title
     *
     * @return Password
     */
    public function setTitle($title = null)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set icon.
     *
     * @param string|null $icon
     *
     * @return Password
     */
    public function setIcon($icon = null)
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get icon.
     *
     * @return string|null
     */
    public function getIcon()
    {
        return $this->icon;
    }

    /**
     * Set description.
     *
     * @param string|null $description
     *
     * @return Password
     */
    public function setDescription($description = null)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description.
     *
     * @return string|null
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set username.
     *
     * @param string|null $username
     *
     * @return Password
     */
    public function setUsername($username = null)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username.
     *
     * @return string|null
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
     * @return Password
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
     * Set url.
     *
     * @param string|null $url
     *
     * @return Password
     */
    public function setUrl($url = null)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url.
     *
     * @return string|null
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set tags.
     *
     * @param string|null $tags
     *
     * @return Password
     */
    public function setTags($tags = null)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Get tags.
     *
     * @return string|null
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Set lastModificationDate.
     *
     * @param \DateTime|null $lastModificationDate
     *
     * @return Password
     */
    public function setLastModificationDate($lastModificationDate = null)
    {
        $this->lastModificationDate = $lastModificationDate;

        return $this;
    }

    /**
     * Get lastModificationDate.
     *
     * @return \DateTime|null
     */
    public function getLastModificationDate()
    {
        return $this->lastModificationDate;
    }

    /**
     * Set folder.
     *
     * @param Folder|null $folder
     *
     * @return Password
     */
    public function setFolder(Folder $folder = null)
    {
        $this->folder = $folder;

        return $this;
    }

    /**
     * Get folder.
     *
     * @return Folder|null
     */
    public function getFolder()
    {
        return $this->folder;
    }
}
