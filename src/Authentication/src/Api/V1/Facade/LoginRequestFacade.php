<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Facade;

use Doctrine\ORM\EntityManager;
use Laminas\I18n\Translator\Translator;
use Authentication\Api\V1\Entity\LoginRequest;
use App\Abstracts\AbstractFacade;
use Exception;

class LoginRequestFacade extends AbstractFacade
{
    /**
     * Constructor
     *
     * @param Translator $translator
     * @param EntityManager $entityManager
     */
    public function __construct(
        Translator $translator,
        EntityManager $entityManager
    ) {
        parent::__construct(
            $translator,
            $entityManager,
            LoginRequest::class
        );
    }

    /**
     * Create new instance of LoginRequest
     * 
     * @param array $data
     * @return LoginRequest
     */
    public function create($data)
    {
        $loginRequest = $this->reflectionHydrator->hydrate(
            $data,
            new LoginRequest()
        );
        $this->persist($loginRequest);

        return $loginRequest;
    }

    /**
     *
     * @param type $id
     * @param type $filter
     */
    public function delete($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $filter
     */
    public function fetch($id, $filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param array $filter
     */
    public function fetchAll($filter): never
    {
        throw new Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $data
     */
    public function update($id, $data): never
    {
        throw new Exception("Method not implemented");
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
