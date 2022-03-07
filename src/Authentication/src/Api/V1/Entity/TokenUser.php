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
 * Permission
 *
 * @ORM\Table(name="token_user")
 * @ORM\Entity
 * @OA\Schema(description="Token table")
 */
class TokenUser
{
    /**
     *
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     * @ORM\OneToOne(targetEntity="User\Api\V1\Entity\User")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=true)
     * })
     */
    private User $user;

    /**
     *
     * @ORM\Column(name="token", type="string", length=500, precision=0, scale=0, nullable=true, unique=true)
     * @OA\Property(property="token", type="string", description="token value", example="ey.token")
     */
    private ?string $token;

    /**
     *
     * @ORM\Column(name="last_login", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="last_login", type="datetime", description="Last login date", example="title")
     */
    private ?\DateTime $lastLogin = null;

    /**
     * Set User.
     *
     * @param User $user
     * @return TokenUser
     */
    public function setUser(User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get User.
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set Token
     *
     * @param string $token
     *
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
     * Set last_login
     *
     * @param datetime $lastLogin
     *
     * @return TokenUser
     */
    public function setLastLogin($lastLogin = null)
    {
        $this->lastLogin = $lastLogin;

        return $this;
    }

    /**
     * Get last_login
     *
     * @param \Datetime $lastLogin
     *
     * @return \Datetime|null
     */
    public function getLastLogin()
    {
        return $this->lastLogin;
    }
}
