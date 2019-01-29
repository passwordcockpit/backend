<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Facade;

use Doctrine\ORM\EntityManager;
use Authentication\Api\V1\Entity\LoginRequest;

class LoginRequestFacade
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Add a failed login request
     *
     * @param string $ip
     * @param string $username
     *
     */
    public function addLoginRequest($ip, $username)
    {
        $loginRequest = new LoginRequest();
        $loginRequest->setIp($ip);
        $loginRequest->setUsername($username);
        $loginRequest->setAttemptDate(
            new \Datetime("now", new \DateTimeZone('Europe/Zurich'))
        );

        $this->entityManager->persist($loginRequest);
        $this->entityManager->flush();

        return true;
    }

    /**
     * @param string $ip
     * @param string $username
     * @param int $time
     *
     * @return LoginRequest[]
     */
    public function getLastAttempts($ip, $username, $time)
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();

        return $queryBuilder
            ->select('f')
            ->from(LoginRequest::class, 'f')
            ->where('f.ip = ?1')
            ->andWhere('f.username = ?2')
            ->andWhere('f.attemptDate > ?3')
            ->setParameter(1, $ip)
            ->setParameter(2, $username)
            ->setParameter(3, $time)
            ->getQuery()
            ->getResult();
    }
}
