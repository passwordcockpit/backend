<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace File\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Password\Api\V1\Entity\Password;

/**
 * File
 *
 * @ORM\Table(name="file")
 * @ORM\Entity
 */
class File
{
    /**
     * @var int
     *
     * @ORM\Column(name="file_id", type="integer", precision=0, scale=0, nullable=false, unique=false)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private $fileId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="name", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="filename", type="string", length=1000, precision=0, scale=0, nullable=true, unique=false)
     */
    private $filename;

    /**
     * @var string|null
     *
     * @ORM\Column(name="extension", type="string", length=200, precision=0, scale=0, nullable=true, unique=false)
     */
    private $extension;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="creation_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     */
    private $creationDate;

    /**
     * @var \Password\Api\V1\Entity\Password
     *
     * @ORM\ManyToOne(targetEntity="Password\Api\V1\Entity\Password")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="password_id", referencedColumnName="password_id", nullable=true)
     * })
     */
    private $password;

    /**
     * Get fileId.
     *
     * @return int
     */
    public function getFileId()
    {
        return $this->fileId;
    }

    /**
     * Set name.
     *
     * @param string|null $name
     *
     * @return File
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
     * Set filename.
     *
     * @param string|null $filename
     *
     * @return File
     */
    public function setFilename($filename = null)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename.
     *
     * @return string|null
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set extension.
     *
     * @param string|null $extension
     *
     * @return File
     */
    public function setExtension($extension = null)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension.
     *
     * @return string|null
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set creationDate.
     *
     * @param \DateTime|null $creationDate
     *
     * @return File
     */
    public function setCreationDate($creationDate = null)
    {
        $this->creationDate = $creationDate;

        return $this;
    }

    /**
     * Get creationDate.
     *
     * @return \DateTime|null
     */
    public function getCreationDate()
    {
        return $this->creationDate;
    }

    /**
     * Set password.
     *
     * @param \Password\Api\V1\Entity\Password|null $password
     *
     * @return File
     */
    public function setPassword(
        \Password\Api\V1\Entity\Password $password = null
    ) {
        $this->password = $password;

        return $this;
    }

    /**
     * Get password.
     *
     * @return \Password\Api\V1\Entity\Password|null
     */
    public function getPassword()
    {
        return $this->password;
    }
}
