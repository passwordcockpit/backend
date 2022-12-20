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

/**
 * Permission
 *
 * @ORM\Table(name="login_request")
 * @ORM\Entity
 * @OA\Schema(description="Login Requests table")
 */
class LoginRequest
{
    /**
     *
     * @ORM\Column(name="request_id", type="integer", precision=0, scale=0, nullable=false, unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    private int $requestId;

    /**
     *
     * @ORM\Column(name="ip", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="ip", type="string", description="ip making the request", example="256.240.20.111")
     */
    private string $ip;

    /**
     *
     * @ORM\Column(name="attempt_date", type="datetime", precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="dateTime", type="datetime", description="Login request date", example="2019-01-22 18:18:18")
     */
    private ?\DateTime $attemptDate = null;

    /**
     *
     * @ORM\Column(name="username", type="string", length=100, precision=0, scale=0, nullable=true, unique=false)
     * @OA\Property(property="username", type="string", description="username on which the request is made", example="admin")
     */
    private string $username;

    /**
     * Set User.
     *
     * @param string $ip
     *
     * @return LoginRequest
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get Ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set username
     *
     * @param string $username
     *
     * @return LoginRequest
     */
    public function setUsername($username = null)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string|null
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set attemptDate
     *
     * @param datetime $attemptDate
     *
     * @return LoginRequest
     */
    public function setAttemptDate($attemptDate = null)
    {
        $this->attemptDate = $attemptDate;

        return $this;
    }

    /**
     * Get attemptDate
     *
     * @param \Datetime $attemptDate
     *
     * @return \Datetime|null
     */
    public function getAttemptDate()
    {
        return $this->attemptDate;
    }
}
