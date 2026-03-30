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
 * @OA\Schema(description="Login Requests table")
 */
#[ORM\Entity]
#[ORM\Table(name: "login_request")]
class LoginRequest
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "request_id", type: "integer")]
    private int $requestId;

    /**
     * @OA\Property(property="ip", type="string", description="ip making the request", example="256.240.20.111")
     */
    #[ORM\Column(name: "ip", type: "string", length: 100, nullable: true)]
    private string $ip;

    /**
     * @OA\Property(property="dateTime", type="datetime", description="Login request date", example="2019-01-22 18:18:18")
     */
    #[ORM\Column(name: "attempt_date", type: "datetime", nullable: true)]
    private ?\DateTime $attemptDate = null;

    /**
     * @OA\Property(property="username", type="string", description="username on which the request is made", example="admin")
     */
    #[ORM\Column(name: "username", type: "string", length: 100, nullable: true)]
    private string $username;

    /**
     * Set ip
     *
     * @param string $ip
     * @return LoginRequest
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Get requestId
     *
     * @return int
     */
    public function getRequestId()
    {
        return $this->requestId;
    }

    /**
     * Set username
     *
     * @param string $username
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
     * @param ?DateTime $attemptDate
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
     * @return \Datetime|null
     */
    public function getAttemptDate()
    {
        return $this->attemptDate;
    }
}
