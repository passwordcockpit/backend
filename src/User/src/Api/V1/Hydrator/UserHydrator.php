<?php

/**
 * Description of UserHydrator
 *
 * @see https://github.com/passwordcockpit/backend for the canonical source repository
 * @copyright Copyright (c) 2018 Blackpoints AG (https://www.blackpoints.ch)
 * @license https://github.com/passwordcockpit/backend/blob/master/LICENSE.md BSD 3-Clause License
 * @author Giona Guidotti <giona.guidotti@blackpoints.ch>
 */

namespace User\Api\V1\Hydrator;

use Laminas\Hydrator\AbstractHydrator;
use User\Api\V1\Entity\User;

class UserHydrator extends AbstractHydrator
{
    /**
     * Extract information from User object
     *
     * @param User $user
     * @return array
     */
    public function extract($user)
    {
        $data = [];
        $data['user_id'] = $user->getUserId();
        $data['username'] = $user->getUsername();
        $data['name'] = $user->getName();
        $data['surname'] = $user->getSurname();
        $data['enabled'] = $user->getEnabled();
        if ($user->getCompleteUser()) {
            $data['phone'] = $user->getPhone();
            $data['email'] = $user->getEmail();
            $data['language'] = $user->getLanguage();
            $data['change_password'] = $user->getChangePassword();
        }
        if (!is_null($user->getAccess())) {
            $data['access'] = $user->getAccess();
        }
        return $data;
    }

    /**
     *
     * @param array $data
     * @param \User\Api\V1\Hydrator\UserEntity $user
     * @return \User\Api\V1\Hydrator\UserEntity
     */
    public function hydrate(array $data, $user)
    {
        if (!$user instanceof User) {
            return $user;
        }
        if ($this->isPropertyAvailable('user_id', $data)) {
            $user->setUserId($data['user_id']);
        }
        if ($this->isPropertyAvailable('username', $data)) {
            $user->setUsername($data['username']);
        }
        if ($this->isPropertyAvailable('password', $data)) {
            $user->setPassword($data['password']);
        }
        if ($this->isPropertyAvailable('name', $data)) {
            $user->setName($data['name']);
        }
        if ($this->isPropertyAvailable('surname', $data)) {
            $user->setSurname($data['surname']);
        }
        if ($this->isPropertyAvailable('phone', $data)) {
            $user->setPhone($data['phone']);
        }
        if ($this->isPropertyAvailable('email', $data)) {
            $user->setEmail($data['email']);
        }
        if ($this->isPropertyAvailable('language', $data)) {
            $user->setLanguage($data['language']);
        }
        if ($this->isPropertyAvailable('enabled', $data)) {
            $user->setEnabled($data['enabled']);
        }
        if ($this->isPropertyAvailable('change_password', $data)) {
            $user->setChangePassword($data['change_password']);
        }
        return $user;
    }

    /**
     * Check whether a property is available for the object
     *
     * @param string $property
     * @param array $data
     * @return bool
     */
    protected function isPropertyAvailable($property, $data)
    {
        return isset($data[$property]) && $data[$property] !== '';
    }
}
