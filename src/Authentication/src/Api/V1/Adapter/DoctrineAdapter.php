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
use User\Api\V1\Entity\User;
use Doctrine\ORM\EntityManager;

class DoctrineAdapter implements AdapterInterface
{
    private ?string $username;

    private ?string $password;

    /**
     * Constructor
     * @param EntityManager $entityManager
     */
    public function __construct(private readonly EntityManager $entityManager)
    {
        $this->username = null;
        $this->password = null;
    }

    /**
     * Set the login password
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password = $password;
    }

    /**
     * Set the login username
     *
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }

    /**
     * Function called by the adapter
     *
     * @return Result
     */
    public function authenticate()
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy(['username' => $this->username]);
        if ($user) {
            $securePass = $user->getPassword();
            if (password_verify($this->password, $securePass)) {
                // Check if user is enabled
                if (!$user->getEnabled()) {
                    $result = new Result(-2, $user, []);
                    return $result;
                }
                // User information are correct
                $result = new Result(1, $user, []);
                return $result;
            } else {
                // Wrong pass
                $result = new Result(-3, $user, []);
                return $result;
            }
        } else {
            // User does not exist
            $result = new Result(0, null, []);
            return $result;
        }
    }
}
