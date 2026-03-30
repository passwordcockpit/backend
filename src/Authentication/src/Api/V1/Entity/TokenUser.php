<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Entity;

use Doctrine\ORM\Mapping as ORM;
use OpenApi\Annotations as OA;
use User\Api\V1\Entity\User;

/**
 * @OA\Schema(description="Token table")
 */
#[ORM\Entity]
#[ORM\Table(name: "token_user")]
class TokenUser
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\OneToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(name: "user_id", referencedColumnName: "user_id", nullable: true)]
    private User $user;

    /**
     * @OA\Property(property="token", type="string", description="token value", example="ey.token")
     */
    #[ORM\Column(name: "token", type: "string", length: 500, nullable: true, unique: true)]
    private ?string $token = null;

    /**
     * @OA\Property(property="last_login", type="datetime", description="Last login date", example="title")
     */
    #[ORM\Column(name: "last_login", type: "datetime", nullable: true)]
    private ?\DateTime $lastLogin = null;

    /**
     * Set user
     *
     * @param User $user
     * @return TokenUser
     */
    public function setUser(?User $user)
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

    /**
     * Set token
     *
     * @param string $token
     * @return TokenUser
     */
    public function setToken($token = null)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get Token
     *
     * @return string|null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set lastLogin
     *
     * @param ?DateTime $lastLogin
     * @return TokenUser
     */
    public function setLastLogin($lastLogin = null)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get lastLogin
     *
     * @param \Datetime $lastLogin
     * @return \Datetime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
}
