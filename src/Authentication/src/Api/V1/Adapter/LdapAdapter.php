<?php

/**
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Davide Bucher <davide.bucher@blackpoints.ch>
 */

namespace Authentication\Api\V1\Adapter;

use Laminas\Authentication\Adapter\AdapterInterface;
use Laminas\Authentication\Result;
use Laminas\Authentication\Adapter\Ldap;
use User\Api\V1\Facade\UserFacade;
use User\Api\V1\Entity\User;
use Doctrine\ORM\EntityManager;

class LdapAdapter implements AdapterInterface
{
    /**
     * @var UserFacade $userFacade
     */
    private $userFacade;

    /**
     * @var string $username
     */
    private $username;

    /**
     * @var string $password
     */
    private $password;

    /**
     * @var array $ldapConfig
     */
    private $ldapConfig;

    /**
     * @var EntityManager $entitymanager
     */
    private $entityManager;

    /**
     * Constructor
     *
     * @param UserFacade $userFacade
     * @param array $ldapConfig
     */
    public function __construct(
        UserFacade $userFacade,
        $ldapConfig,
        $entityManager
    ) {
        $this->userFacade = $userFacade;
        $this->ldapConfig = $ldapConfig;
        $this->entityManager = $entityManager;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function authenticate()
    {
        $ldap = new Ldap($this->ldapConfig);
        $ldap->setIdentity($this->username);
        $ldap->setCredential($this->password);
        $result = $ldap->authenticate();
        if ($result->isValid()) {
            // match LDAP user with DB user
            $ldapUser = $ldap->getAccountObject();
            $user = $this->userFacade->getUserByUsername($ldapUser->uid);
            if ($user && $user->getEnabled()) {
                // update User in DB
                $user->setName($ldapUser->givenname);
                $user->setSurname($ldapUser->sn);
                $user->setPhone();
                $user->setEmail($ldapUser->mail);
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                $result = new Result(1, $user, []);
                return $result;
            } else {
                $result = new Result(-2, $user, []);
                return $result;
            }
        } else {
            $result = new Result(0, null, []);
            return $result;
        }
    }
}
