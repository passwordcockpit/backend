<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace User\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use \User\Api\V1\Entity\User;

/**
 * @OA\Schema(description="Permission")
 */
#[ORM\Entity]
#[ORM\Table(name: "permission")]
class Permission
{
    public function __construct(
        /**
         * @OA\Property(property="manage_users")
         */
        #[ORM\Column(name: "manage_users", type: "boolean", nullable: true)]
        private bool $manageUsers,

        /**
         * @OA\Property(property="create_folders")
         */
        #[ORM\Column(name: "create_folders", type: "boolean", nullable: true)]
        private bool $createFolders,

        /**
         * @OA\Property(property="access_all_folders")
         */
        #[ORM\Column(name: "access_all_folders", type: "boolean", nullable: true)]
        private bool $accessAllFolders,

        /**
         * @OA\Property(property="view_logs")
         */
        #[ORM\Column(name: "view_logs", type: "boolean", nullable: true)]
        private bool $viewLogs,

        #[ORM\Id]
        #[ORM\GeneratedValue(strategy: "NONE")]
        #[ORM\OneToOne(targetEntity: User::class)]
        #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id")]
        private User $user
    )
    {
    }

    /**
     * Set manageUsers
     *
     * @param bool|null $manageUsers
     * @return Permission
     */
    public function setManageUsers($manageUsers = null)
    {
        $this->manageUsers = $manageUsers;
        return $this;
    }

    /**
     * Get manageUsers
     *
     * @return bool|null
     */
    public function getManageUsers()
    {
        return $this->manageUsers;
    }

    /**
     * Set createFolders
     *
     * @param bool|null $createFolders
     * @return Permission
     */
    public function setCreateFolders($createFolders = null)
    {
        $this->createFolders = $createFolders;
        return $this;
    }

    /**
     * Get createFolders
     *
     * @return bool|null
     */
    public function getCreateFolders()
    {
        return $this->createFolders;
    }

    /**
     * Set accessAllFolders
     *
     * @param bool|null $accessAllFolders
     * @return Permission
     */
    public function setAccessAllFolders($accessAllFolders = null)
    {
        $this->accessAllFolders = $accessAllFolders;
        return $this;
    }

    /**
     * Get accessAllFolders
     *
     * @return bool|null
     */
    public function getAccessAllFolders()
    {
        return $this->accessAllFolders;
    }

    /**
     * Set viewLogs
     *
     * @param bool|null $viewLogs
     * @return Permission
     */
    public function setViewLogs($viewLogs = null)
    {
        $this->viewLogs = $viewLogs;
        return $this;
    }

    /**
     * Get viewLogs
     *
     * @return bool|null
     */
    public function getViewLogs()
    {
        return $this->viewLogs;
    }

    /**
     * Set user
     *
     * @param User $user
     * @return Permission
     */
    public function setUser(User $user)
    {
        $this->user = $user;
        return $this;
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
