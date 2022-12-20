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
use Authentication\Api\V1\Entity\TokenUser;
use App\Abstracts\AbstractFacade;
use User\Api\V1\Entity\User;

class TokenUserFacade extends AbstractFacade
{
    /**
     * Constructor
     *
     * @param Translator $translator
     * @param EntityManager $entityManager
     */
    public function __construct(
        protected Translator $translator,
        protected EntityManager $entityManager
    ) {
        parent::__construct($translator, $entityManager, TokenUser::class);
    }

    /**
     *
     * @param array $data
     */
    public function create($data): never
    {
        throw new \Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $filter
     */
    public function fetch($id, $filter): never
    {
        throw new \Exception("Method not implemented");
    }

    /**
     *
     * @param array $filter
     */
    public function fetchAll($filter): never
    {
        throw new \Exception("Method not implemented");
    }

    /**
     *
     * @param string $id
     * @param array $data
     */
    public function update($id, $data): never
    {
        throw new \Exception("Method not implemented");
    }

    /**
     *
     * @param type $id
     * @param type $filter
     */
    public function delete($id, $filter): never
    {
        throw new \Exception("Method not implemented");
    }

    /**
     * Return a tokenUser
     *
     * @param string $token
     *
     * @return TokenUser|null
     *
     */
    public function getByToken($token)
    {
        $tokenUser = $this->entityManager
            ->getRepository(TokenUser::class)
            ->findBy(['token' => $token]);

        return $tokenUser;
    }

    /**
     * Return a tokenUser
     *
     * @param int $userId
     *
     * @return TokenUser|null
     *
     */
    public function getByUserId($userId)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findBy(['userId' => $userId]);

        $tokenUser = $this->entityManager
            ->getRepository(TokenUser::class)
            ->findBy(['user' => $user]);

        return $tokenUser;
    }

    /**
     * Create a tokenUser
     *
     * @param User $user
     * @param string $token
     *
     * @return bool true
     *
     */
    public function createTokenUser($user, $token)
    {
        $tokenUser = new TokenUser();
        $tokenUser->setUser($user);
        $tokenUser->setToken($token);
        $tokenUser->setLastLogin(
            new \Datetime("now", new \DateTimeZone('Europe/Zurich'))
        );

        $this->entityManager->persist($tokenUser);
        $this->entityManager->flush();

        return true;
    }

    /**
     * Updates a tokenUser
     *
     * @param TokenUser $tokenUser
     * @param string $token
     * @param bool $modifyDate
     *
     */
    public function updateTokenUser($tokenUser, $token, $modifyDate = true)
    {
        $tokenUser->setToken($token);
        if ($modifyDate) {
            $tokenUser->setLastLogin(
                new \Datetime("now", new \DateTimeZone('Europe/Zurich'))
            );
        }
        $this->entityManager->persist($tokenUser);
        $this->entityManager->flush();
    }

    /**
     * Delete token from a TokenUser
     *
     * @param TokenUser $tokenUser
     *
     */
    public function deleteToken($tokenUser)
    {
        $tokenUser->setToken(null);
        $this->entityManager->persist($tokenUser);
        $this->entityManager->flush();
    }
}
