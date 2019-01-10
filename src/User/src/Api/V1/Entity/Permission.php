<?php

/**
 * @see https://github.com/password-cockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/password-cockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace User\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * Permission
 *
 * @ORM\Table(name="permission")
 * @ORM\Entity
 * @SWG\Definition(definition="Permission")
 */
class Permission
{
    /**
     * @var bool
     *
     * @ORM\Column(name="manage_users", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="manage_users")
     */
    private $manageUsers;

    /**
     * @var bool
     *
     * @ORM\Column(name="create_folders", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="create_folders")
     */
    private $createFolders;

    /**
     * @var bool
     *
     * @ORM\Column(name="access_all_folders", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="access_all_folders")
     */
    private $accessAllFolders;

    /**
     * @var bool
     *
     * @ORM\Column(name="view_logs", type="boolean", precision=0, scale=0, nullable=true, unique=false)
     *
     * @SWG\Property(property="view_logs")
     */
    private $viewLogs;

    /**
     * @var \User\Api\V1\Entity\User
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User\Api\V1\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=true)
     * })
     */
    private $user;

    public function __construct(
        $manageUsers,
        $createFolders,
        $accessAllFolders,
        $viewLogs,
        $user
    ) {
        $this->manageUsers = $manageUsers;
        $this->createFolders = $createFolders;
        $this->accessAllFolders = $accessAllFolders;
        $this->viewLogs = $viewLogs;
        $this->user = $user;
    }

    /**
     * Set manageUsers.
     *
     * @param bool|null $manageUsers
     *
     * @return Permission
     */
    public function setManageUsers($manageUsers = null)
    {
        $this->manageUsers = $manageUsers;

        return $this;
    }

    /**
     * Get manageUsers.
     *
     * @return bool|null
     */
    public function getManageUsers()
    {
        return $this->manageUsers;
    }

    /**
     * Set createFolders.
     *
     * @param bool|null $createFolders
     *
     * @return Permission
     */
    public function setCreateFolders($createFolders = null)
    {
        $this->createFolders = $createFolders;

        return $this;
    }

    /**
     * Get createFolders.
     *
     * @return bool|null
     */
    public function getCreateFolders()
    {
        return $this->createFolders;
    }

    /**
     * Set accessAllFolders.
     *
     * @param bool|null $accessAllFolders
     *
     * @return Permission
     */
    public function setAccessAllFolders($accessAllFolders = null)
    {
        $this->accessAllFolders = $accessAllFolders;

        return $this;
    }

    /**
     * Get accessAllFolders.
     *
     * @return bool|null
     */
    public function getAccessAllFolders()
    {
        return $this->accessAllFolders;
    }

    /**
     * Set viewLogs.
     *
     * @param bool|null $viewLogs
     *
     * @return Permission
     */
    public function setViewLogs($viewLogs = null)
    {
        $this->viewLogs = $viewLogs;

        return $this;
    }

    /**
     * Get viewLogs.
     *
     * @return bool|null
     */
    public function getViewLogs()
    {
        return $this->viewLogs;
    }

    /**
     * Set user.
     *
     * @param \User\Api\V1\Entity\User $user
     *
     * @return Permission
     */
    public function setUser(\User\Api\V1\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \User\Api\V1\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
