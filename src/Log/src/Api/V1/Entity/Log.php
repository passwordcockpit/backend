<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 */

namespace Log\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use Password\Api\V1\Entity\Password;
use User\Api\V1\Entity\User;

/**
 * @OA\Schema(description="Log")
 */
#[ORM\Entity]
#[ORM\Table(name: "log")]
class Log
{
    /**
     * @OA\Property
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "log_id", type: "integer")]
    private int $logId;

    /**
     * @OA\Property
     */
    #[ORM\Column(name: "action_date", type: "datetime", nullable: true)]
    private ?\DateTime $actionDate = null;

    /**
     * @OA\Property
     */
    #[ORM\Column(name: "action", type: "string", length: 4000, nullable: true)]
    private ?string $action = null;

    /**
     * @OA\Property
     */
    #[ORM\ManyToOne(targetEntity: Password::class)]
    #[ORM\JoinColumn(name: "password_id", referencedColumnName: "password_id", nullable: true)]
    private ?Password $password = null;

    /**
     * @OA\Property
     */
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", nullable: true)]
    private User $user;

    /**
     * Get logId
     *
     * @return int
     */
    public function getLogId()
    {
        return $this->logId;
    }

    /**
     * Set actionDate
     *
     * @param \DateTime|null $actionDate
     * @return Log
     */
    public function setActionDate($actionDate = null)
    {
        $this->actionDate = $actionDate;
        return $this;
    }

    /**
     * Get actionDate
     *
     * @return \DateTime|null
     */
    public function getActionDate()
    {
        return $this->actionDate;
    }

    /**
     * Set action
     *
     * @param string|null $action
     * @return Log
     */
    public function setAction($action = null)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * Get action
     *
     * @return string|null
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set password
     *
     * @param Password|null $password
     * @return Log
     */
    public function setPassword(?Password $password) {
        $this->password = $password;
        return $this;
    }

    /**
     * Get password
     *
     * @return Password|null
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set user
     *
     * @param User|null $user
     * @return Log
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
